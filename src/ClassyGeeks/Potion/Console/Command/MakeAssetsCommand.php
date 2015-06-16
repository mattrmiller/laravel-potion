<?php
/**
 * Copyright 2015 Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

/**
 * Namespace
 */
namespace ClassyGeeks\Potion\Console\Command;

use Illuminate\Support\Facades\Cache;
use Illuminate\Console\Command;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\OptiPngFilter;
use Assetic\Filter\JpegoptimFilter;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\Yui\CssCompressorFilter;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\JSqueezeFilter;
use Assetic\Filter\ScssphpFilter;
use Assetic\Filter\Yui\JsCompressorFilter;

/**
 * Class MakeAssetsCommand
 * @package ClassyGeeks\Potion\Console\Command
 */
class MakeAssetsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'potion:make-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make potion assets.';

    /**
     * Global potion config
     * @var array
     */
    protected $config;

    /**
     * Create a new command instance.
     * @param $config
     * @return void
     */
    public function __construct($config)
    {
        // Parent
        parent::__construct();

        // Save config
        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        try {

            // Potion config
            if ($this->config === false) {
                throw new \Exception('Invalid potion configuration, please run "artisan vendor:publish" in your project root to public the potion config file.');
            }

            // Clean up paths
            $this->config['resource_path'] = rtrim($this->config['resource_path'], '/');
            $this->config['resource_path'] = rtrim($this->config['resource_path'], '\\');
            $this->config['assets_path'] = rtrim($this->config['assets_path'], '/');
            $this->config['assets_path'] = rtrim($this->config['assets_path'], '\\');

            // Make the assets path
            if (!$this->makePath($this->config['assets_path'])) {
                throw new \Exception("Unable to make assets_path from config: {$this->config['assets_path']}");
            }

            // Filters
            $filters = [];
            // -- optipng
            $filter = new OptiPngFilter($this->config['filters']['optipng']['path']);
            $filter->setLevel($this->config['filters']['optipng']['level']);
            $filters['optipng'] = $filter;
            // -- jpegoptim
            $filter = new JpegoptimFilter($this->config['filters']['jpegoptim']['path']);
            $filter->setStripAll($this->config['filters']['jpegoptim']['strip']);
            $filter->setMax($this->config['filters']['jpegoptim']['max']);
            $filters['jpegoptim'] = $filter;
            // -- Css import
            $filter = new CssImportFilter();
            $filters['css_import'] = $filter;
            // -- Css rewrite
            $filter = new CssRewriteFilter();
            $filters['css_rewrite'] = $filter;
            // -- Css min
            $filter = new CssMinFilter();
            $filters['css_min'] = $filter;
            // -- Css Yui
            $filter = new CssCompressorFilter($this->config['filters']['css_yui']['path_jar'], $this->config['filters']['css_yui']['path_java']);
            $filters['css_yui'] = $filter;
            // -- CSS LessPHP
            $filter = new LessphpFilter();
            $filter->setLoadPaths($this->config['filters']['css_lessphp']['path_imports']);
            $filter->setFormatter($this->config['filters']['css_lessphp']['format']);
            $filter->setPreserveComments($this->config['filters']['css_lessphp']['preserve_comments']);
            $filters['css_lessphp'] = $filter;
            // -- CSS ScssPHP
            $filter = new ScssphpFilter();
            $filter->setImportPaths($this->config['filters']['css_scssphp']['path_imports']);
            $filter->setFormatter($this->config['filters']['css_scssphp']['format']);
            $filters['css_scssphp'] = $filter;
            // -- JS Min
            $filter = new JSMinFilter();
            $filters['js_min'] = $filter;
            // -- Js Yui
            $filter = new JsCompressorFilter($this->config['filters']['js_yui']['path_jar'], $this->config['filters']['js_yui']['path_java']);
            $filter->setNomunge($this->config['filters']['js_yui']['no_munge']);
            $filter->setPreserveSemi($this->config['filters']['js_yui']['preserve_semi']);
            $filter->setDisableOptimizations($this->config['filters']['js_yui']['disable_opti']);
            $filters['js_yui'] = $filter;

            // Cache
            $cache = [];

            // Each potion
            foreach ($this->config['potions'] as $potion) {

                // -- Find assets
                $resource_filters = [];
                foreach ($potion['filters'] as $filter) {
                    $resource_filters[] = $filters[$filter];
                }

                // -- Asset content
                $asset_content = '';

                // -- Resources
                foreach ($potion['resources'] as $resource) {

                    // -- -- Make full path
                    $resource = ltrim($resource, '/');
                    $resource = ltrim($resource, '\\');
                    $asset_path = $this->config['resource_path'] . DIRECTORY_SEPARATOR . $resource;

                    // -- -- Echo
                    $this->info("Processing resource: {$asset_path}");

                    // -- -- Get path info
                    $pathinfo = pathinfo($asset_path);

                    // -- -- File assets
                    $file_assets = [];

                    // -- -- Glob?
                    if ($pathinfo['extension'] == '*' || $pathinfo['filename'] == '*') {

                        // -- -- -- Get all file assets
                        $glob = new GlobAsset($asset_path, $resource_filters);
                        foreach ($glob->all() as $file_asset) {
                            $file_assets[] = new FileAsset(rtrim($file_asset->getSourceRoot(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file_asset->getSourcePath(), $resource_filters);
                        }
                    }
                    else {
                        $file_assets[] = new FileAsset($asset_path, $resource_filters);
                    }

                    // -- -- Each file asset
                    foreach ($file_assets as $file_asset) {

                        // -- -- -- File
                        $file_path = $this->config['assets_path'] . DIRECTORY_SEPARATOR . $file_asset->getSourcePath();

                        // -- -- -- Echo
                        $this->info("Processing resource file: {$file_path}");

                        // -- -- -- Make file, or combine
                        if ($potion['output'] !== false) {
                            $asset_content .= $file_asset->dump();
                        }
                        else {

                            // -- -- -- -- Echo
                            $this->info("Writing asset file: {$file_path}");

                            // -- -- -- -- Write
                            if (file_put_contents($file_path, $file_asset->dump()) === false) {
                                $this->error("Error writing asset file: {$file_path}");
                            }

                            // -- -- -- -- Add to cache
                            $cache[$file_asset->getSourcePath()] = $this->versionFile($file_path);
                        }
                    }
                }

                // -- Combine to a single file
                if ($potion['output'] !== false) {

                    // -- -- Write to file
                    $file_path = $this->config['assets_path'] . DIRECTORY_SEPARATOR . $potion['output'];

                    // -- -- Echo
                    $this->info("Writing asset file: {$file_path}");

                    // -- -- Write
                    if (file_put_contents($file_path, $asset_content) === false) {
                        $this->error("Error writing asset file: {$file_path}");
                    }

                    // -- -- Add to cache
                    $cache[$potion['output']] = $this->versionFile($file_path);
                }
            }

            // Set cache
            Cache::forever('potion_assets', $cache);

        }
        catch (\Exception $e) {

            // Echo
            $this->error($e->getMessage());

        }
    }

    /**
     * Version file
     * @param $file_path
     * @return string
     */
    protected function versionFile($file_path)
    {
        return sha1_file($file_path);
    }

    /**
     * Make path
     *
     * @param $path
     * @return bool
     */
    protected function makePath($path)
    {
        // Make
        if (!is_dir($path)) {
            if (mkdir($path) === false) {
                return false;
            }
        }

        // Make writable
        if (!is_writable($path)) {
            if (chmod($path, 0777) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}