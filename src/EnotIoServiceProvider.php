<?php

namespace Weishaypt\EnotIo;

use Illuminate\Support\ServiceProvider;

class EnotIoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/enotio.php' => config_path('enotio.php'),
        ], 'config');

        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enotio.php', 'enotio');

        $this->app->singleton('enotio', function () {
            return $this->app->make(EnotIo::class);
        });

        $this->app->alias('enotio', 'EnotIo');

        //
    }
}
