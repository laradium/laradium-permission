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
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium-permission');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
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
