<?php

namespace Laradium\Laradium\Content\Providers;

use Illuminate\Support\ServiceProvider;

class LaradiumPermissionServiceProvider extends ServiceProvider
{

    /**
     * Boot
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
