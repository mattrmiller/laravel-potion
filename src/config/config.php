<?php
/**
 * Copyright 2016 Matthew R. Miller via Classy Geeks llc. All Rights Reserved
 * http://classygeeks.com
 * MIT License:
 * http://opensource.org/licenses/MIT
 */

/**
 * Resources are in your laravel project.
 * Assets are the final result of your compiled resources.
 */

return [

    // Resources root path, used as the base path, for all resources
    'resource_path' => base_path('resources/assets'),

    // Assets root path, used as the base path, for all published assets
    'assets_path' => public_path('assets'),

    // Base url for referencing potion assets in blade templates
    'base_url' => '/assets',

    // Potions
    'potions' => [

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/css/*.css',
                '/css/*.scss'
            ],

            // Filters to use, from filters below
            'filters' => [
                'css_import',
                'css_rewrite',
                'css_min',
                'css_yui'
            ],

            // Output file name, this signifies you are combining all resources together,
            // putting false will generate each file with original file names
            'output' => 'app.css'
        ],

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/js/global/*.js'
            ],

            // Filters to use, from filters below
            'filters' => [
                'js_squeeze'
            ],

            // Output file name, this signifies you are combining all resources together,
            // putting false will generate each file with original file names
            'output' => 'app.js'
        ],

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/js/pages/*.js'
            ],

            // Filters to use, from filters below
            'filters' => [
                'js_squeeze'
            ],

            // Output file name, this signifies you are combining all resources together,
            // putting false will generate each file with original file names
            'output' => false
        ],

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/img/*.jpeg',
                '/img/*.jpg'
            ],

            // Filters to use, from filters below
            'filters' => [
                'jpegoptim'
            ],

            // Output file name, this signifies you are combining all resources together, putting false will generate each file with original file names
            'output' => false
        ],

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/img/*.png'
            ],

            // Filters to use, from filters below
            'filters' => [
                'optipng'
            ],

            // Output file name, this signifies you are combining all resources together, putting false will generate each file with original file names
            'output' => false
        ],

        [
            // Resources, relative paths to 'resource_path' above, can be any valid file or glob pattern
            'resources' => [
                '/img/*.ico'
            ],

            // Filters to use, from filters below
            'filters' => [
            ],

            // Output file name, this signifies you are combining all resources together, putting false will generate each file with original file names
            'output' => false
        ]

    ],

    // Filters
    'filters' => [

        // JS Min settings
        'js_min' => [

        ],

        // JS Squeeze settings
        'js_squeeze' => [

            // Single line
            'single_line' => true,

            // Keep important comments
            'keep_imp_comments' => false

        ],

        // Yui JS compressor settings
        'js_yui' => [

            // Path to yui jar
            'path_jar' => '/usr/share/yui-compressor/yui-compressor.jar',

            // Path to java binary
            'path_java' => '/usr/bin/java',

            // No munge
            'no_munge' => false,

            // Preserve semi
            'preserve_semi' => true,

            // Disable optimizations
            'disable_opti' => true

        ],

        // Css Import settings
        'css_import' => [

        ],

        // CSS Rewrite settings
        'css_rewrite' => [

        ],

        // Css Min settings
        'css_min' => [

        ],

        // Yui CSS compressor settings
        'css_yui' => [

            // Path to yui jar
            'path_jar' => '/usr/share/yui-compressor/yui-compressor.jar',

            // Path to java binary
            'path_java' => '/usr/bin/java'

        ],

        // LessPhp settings
        'css_lessphp' => [

            // Import paths
            'path_imports' => [
                base_path('resources/assets/css')
            ],

            // Format ('', lessjs, compressed, classic)
            'format' => '',

            // Preserve comments
            'preserve_comments' => false

        ],

        // ScssPhp settings
        'css_scssphp' => [

            // Import paths
            'path_imports' => [
                base_path('resources/assets/css')
            ],

            // Format (scss_formatter, scss_formatter_nested, scss_formatter_compressed)
            'format' => 'scss_formatter_nested',

        ],

        // OptiPng settings, requires being installed on your server, will be used for (PNG, BMP, GIF, PNM or TIFF)
        'optipng' => [

            // Path to optipng binary
            'path' => '/usr/bin/optipng',

            // Level (0-7) (7 is highest)
            'level' => 2

        ],

        // JpegOptim settings, requires being installed on your server, will be used for (JPG, JPEG)
        'jpegoptim' => [

            // Path to jpegoptim binary
            'path' => '/usr/bin/jpegoptim',

            // Strip comment & exif markers
            'strip' => true,

            // Maximum image quality factor
            'max' => 80

        ]

    ]

];