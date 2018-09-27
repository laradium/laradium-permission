<?php

namespace Laradium\Laradium\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class PermissionGroup extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function routes()
    {
        return $this->hasMany(PermissionGroupRoute::class);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [
            '*'         => '*',
            '*.index'   => '*.index',
            '*.create'  => '*.create',
            '*.edit'    => '*.edit',
            '*.destroy' => '*.destroy',
        ];

        foreach (Route::getRoutes() as $route) {
            if ($route->getName()) {
                $exploded = explode('.', $route->getName());
                if (isset($exploded[0])) {
                    if ($exploded[0] !== 'admin') {
                        continue;
                    }

                    if ($exploded[1] === '') {
                        continue;
                    }

                    if (isset($exploded[1]) && in_array($exploded[1], $this->removeRoutes())) {
                        continue;
                    }

                    if (isset($exploded[2]) && in_array($exploded[2], $this->removeActions())) {
                        continue;
                    }

                    $partial = $exploded[0] . '.' . $exploded[1] . '.*';
                    $routes[$partial] = $partial;
                    $routes[$route->getName()] = $route->getName();
                }
            }
        }

        return $routes;
    }

    /**
     * @return array
     */
    protected function removeActions()
    {
        return [
            'data-table',
            'form',
            'store',
            'update',
            'editable'
        ];
    }

    /**
     * @return array
     */
    protected function removeRoutes()
    {
        return [
            'resource',
            'access-denied',
            'dashboard',
            'index'
        ];
    }
}
