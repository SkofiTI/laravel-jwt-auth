<?php

namespace Skofi\LaravelJwtAuth;

use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Skofi\LaravelJwtAuth\Console\InstallCommand::class,
            ]);
        }
    }
}
