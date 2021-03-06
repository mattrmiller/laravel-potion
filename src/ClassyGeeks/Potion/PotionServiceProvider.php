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
namespace ClassyGeeks\Potion;

use Illuminate\Support\ServiceProvider;
use ClassyGeeks\Potion\Console\Command\MakeAssetsCommand;
use ClassyGeeks\Potion\Console\Command\ClearAssetsCommand;

/**
 * Class PotionServiceProvider
 * @package Classygeeks\Potion
 */
class PotionServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Handle config file
        // -- -- Get path
        $config_file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        // -- -- Merge from config
        $this->mergeConfigFrom($config_file, 'potion');
        // -- -- Tell laravel that we publish this file
        $this->publishes([
            $config_file => config_path('potion.php')
        ], 'config');

        // Register blade extensions
        $this->registerBladeExtensions();

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Make assets command
        $this->app->bind('potion.make-assets', function() {
            return new MakeAssetsCommand($this->getConfig());
        });

        // Clear assets command
        $this->app->singleton('potion.clear-assets', function() {
            return new ClearAssetsCommand($this->getConfig());
        });

        // Register commands
        $this->commands([
            'potion.make-assets',
            'potion.clear-assets'
        ]);
    }

    /**
     * Get config
     * @return array
     */
    public function getConfig()
    {
        return $this->app['config']['potion'];
    }

    /**
     * Register blade extensions
     */
    protected function registerBladeExtensions()
    {
        // Potion asset url
        \Blade::extend(function($view, $compiler)
        {
            $pattern = $this->createBladeMatcher('potion_asset_url');
            return preg_replace($pattern, '$1<?php echo(\ClassyGeeks\Potion\BladeHelpers::assetUrl$2); ?>', $view);
        });

        // Potion Css
        \Blade::extend(function($view, $compiler)
        {
            $pattern = $this->createBladeMatcher('potion_asset_css');
            return preg_replace($pattern, '$1<?php echo(\ClassyGeeks\Potion\BladeHelpers::assetCss$2); ?>', $view);
        });

        // Potion Js
        \Blade::extend(function($view, $compiler)
        {
            $pattern = $this->createBladeMatcher('potion_asset_js');
            return preg_replace($pattern, '$1<?php echo(\ClassyGeeks\Potion\BladeHelpers::assetJs$2); ?>', $view);
        });

        // Potion Img
        \Blade::extend(function($view, $compiler)
        {
            $pattern = $this->createBladeMatcher('potion_asset_img');
            return preg_replace($pattern, '$1<?php echo(\ClassyGeeks\Potion\BladeHelpers::assetImg$2); ?>', $view);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['potion'];
    }

    /**
     * Create blade
     * @param $function
     * @return string
     */
    protected function createBladeMatcher($function)
    {
        return '/(?<!\w)(\s*)@'.$function.'(\s*\(.*\))/';
    }

}
