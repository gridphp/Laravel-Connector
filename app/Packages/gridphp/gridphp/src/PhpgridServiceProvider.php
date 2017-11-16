<?php

namespace Gridphp\Gridphp;

use Route;
use Illuminate\Routing\Router;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class PhpgridServiceProvider extends ServiceProvider
{
    protected $packageFilename;

    protected $packagePath;

    protected $namespace;

    protected $namespaceControllers;

    protected $configFile;


    protected function wireUp(){

        $this->packageFilename      = with(new \ReflectionClass(static::class))->getFileName();

        $this->packagePath          = dirname($this->packageFilename);

        $this->namespace            = "Gridphp\Gridphp";

        $this->namespaceControllers = "Gridphp\Gridphp\Http\Controllers";

        $this->configFile = $this->packagePath . "/config/phpgrid.php";

    }

    protected function loadJqgrid()
    {
        config("phpgrid.full_version") ? $add_full_version = "_full" : $add_full_version = "";

        include("{$this->packagePath}/Plugins/lib/inc/jqgrid_dist{$add_full_version}.php");
    }


    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->wireUp();

        /*
         * Registering service
         */
        $this->app->bind('Phpgrid', Phpgrid::class);

        /*
         * Adding configs
         */
        $this->mergeConfigFrom($this->configFile, 'phpgrid');

        /*
         * Adding routes
         */
        Route::middleware('web')
            ->namespace($this->namespaceControllers)
            ->group($this->packagePath . "/routes/web.php");

        /*
         * Adding views
         */
        $this->loadViewsFrom($this->packagePath . "/resources/views", 'phpgrid');


        /*
         * Loading the plugin class
         */
        $this->loadJqgrid();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        AliasLoader::getInstance()->alias(
            'Phpgrid',
            Phpgrid::class
        );


        /*
         * Publishing directives
         */
        $this->publishes([
            __DIR__ . "/config/" => config_path(),
        ], 'config');

        $this->publishes([
            __DIR__ . "/Plugins/bootstrap"  => public_path('/vendor/phpgrid/bootstrap'),
            __DIR__ . "/Plugins/lib/js"     => public_path('/vendor/phpgrid/js'),
        ], 'assets');


    }
}