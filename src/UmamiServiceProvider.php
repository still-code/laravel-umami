<?php

namespace Umami;

use Illuminate\Support\ServiceProvider;

class UmamiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('umami.php'),
            ], 'umami-config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'umami');

        $this->app->singleton('umami', function () {
            return new Umami();
        });
    }
}
