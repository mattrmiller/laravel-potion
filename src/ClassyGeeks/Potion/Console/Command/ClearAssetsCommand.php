<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
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
use Symfony\Component\Console\Input\InputOption;

/**
 * Class ClearAssetsCommand
 * @package ClassyGeeks\Potion\Console\Command
 */
class ClearAssetsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'potion:clear-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear potion assets.';

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
                throw new \Exception('Invalid potion config, please run "artisan vendor:publish" in your project root to public the potion config file.');
            }

            // Clean up paths
            $this->config['assets_path'] = rtrim($this->config['assets_path'], '/');
            $this->config['assets_path'] = rtrim($this->config['assets_path'], '\\');

            // Make sure assets direction exists
            if (!is_dir($this->config['assets_path'])) {
                throw new \Exception("Invalid assets folder: {$this->config['assets_path']}");
            }

            // Confirm that the user wants to clear
            $force = $this->input->getOption('force');
            if (empty($force)) {
                $this->info("This will clear all assets in: {$this->config['assets_path']}");
                $confirmed = $this->confirm('Do you really wish to run this command? [y/N]');
                if (!$confirmed) {
                    $this->comment('Command Cancelled!');
                    return false;
                }
            }

            // Delete all files, do not delete recursively as that is dangerous
            $files = glob($this->config['assets_path'] . DIRECTORY_SEPARATOR . '*');
            foreach ($files as $file) {

                // -- Sanity check
                if (is_file($file)) {

                    // -- -- Echo
                    $this->info("Deleting assets: {$file}");

                    // -- -- Delete
                    unlink($file);
                }
            }

            // Clear cache
            Cache::forget('potion_assets');

        }
        catch (\Exception $e) {

            // Echo
            $this->error($e->getMessage());

        }
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
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation.'],
        ];
    }
}