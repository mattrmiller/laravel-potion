laravel-potion
===============
Potion is a pure PHP asset manager for Laravel 5 based off of [Assetic](https://github.com/kriswallsmith/assetic).

###Description
Laravel 5 comes with a great asset manager called Elixir. While there is nothing wrong with Elixir, it requires you to install Node.js, Gulp, and dependent NPM packages on all of your web serves. While there is nothing wrong with this if you have other needs for those technologies, it seemed unnecessary to us to install that stack solely for the sake of handling assets. So we wrong Potion. Potion is a pure PHP solution, based off of [Assetic](https://github.com/kriswallsmith/assetic) that allows you to handle your assets in the same technology stack that your application is written in.

When using Potion the you will often see is "resources" and "assets". Think of resources as the raw resources inside of Laravel resources direction. Think of assets as what Potion will generate and will ultimately be served to visitors.

###Features
 - Fully integrated into Laravels' artisan commands
 - Asset versioning support
 - Asset CDN Url support
 - Bade Helpers for Asset inclusion in templates
 - Command to clear all assets already published on disk
 - Makes use of Cache configuration, and not disk, in order to account for load balanced servers.
 - Supports the following filters from Assetic:
	 - OptiPngFilter
	 - CssImportFilter
 	 - CssRewriteFilter
	 - CssMinFilter
 	 - CssCompressorFilter from YUI
 	 - LessphpFilter
 	 - JSMinFilter
	 - JpegoptimFilter
 	 - JSqueezeFilter
 	 - JsCompressorFilter from YUI
 	 - ScssphpFilter
 
 ###Installation
1) Add package to your composer.json file:
> require: "classygeeks/potion": "dev-master"

2) Add the Potion Service provider to your config/app.php file under the predefined "providers" array:
> 'providers' => [
>     'Illuminate\Foundation\Providers\ArtisanServiceProvider',
>     ...
>     'ClassyGeeks\Potion\PotionServiceProvider'
>     ...
>     ],
    
3) Publish the config file
   > php artisan vendor:publish

You will now see to new Potion artisan commands. The configuration is very well documented and should be able to get even the most complex projects going quickly.

###To Do
 - Unit tests
 - Support for more filters from Assetic


