<?php

namespace Laradium\Laradium\Permission\Providers;

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
