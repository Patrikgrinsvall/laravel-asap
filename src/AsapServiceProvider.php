<?php

namespace SilentRidge\Asap;

use Illuminate\Support\ServiceProvider;
use SilentRidge\Asap\Commands\FreshCommand;


class AsapServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/asap-config.php' => config_path('laravel-asap.php'),
            ], 'asap-config');


            // Registering package commands.
            $this->commands([
                Commands\DatabaseCommand::class,
                    Commands\SeederCommand::class,
                    Commands\PolicyCommand::class,
                    Commands\FactoryCommand::class,
                    Commands\ModelCommand::class,
                    /**
                     * Third party used
                     * relise:code
                     *
                     */
                Commands\FrontendCommand::class,
                    Commands\NovaCommand::class,
                    Commands\PolicyCommand::class,

            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/asap-config.php', 'laravel-asap');

        // Register the main class to use with the facade
        /*
        $this->app->singleton('laravel-asap', function () {
            return new Asap;
        });
        */
    }
}
