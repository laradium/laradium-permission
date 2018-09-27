<?php

namespace Laradium\Laradium\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Permission\Models\PermissionRole;

trait UserPermissions
{

    /**
     * @return BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(PermissionRole::class);
    }

    /**
     * @param null $resource
     * @return bool
     */
    public function hasPermissionTo($resource = null, $action = 'index'): bool
    {
        if ($resource) {
            $currentRoute = $resource instanceof AbstractResource ? $resource->getRoute($action) : (new $resource)->getRoute($action);
        } else {
            $currentRoute = request()->route()->getName();
        }

        if ($currentRoute === 'admin.access-denied') {
            return true;
        }

        $role = $this->role;
        if (!$role) {
            return false;
        }

        $groups = $role->groups;
        if (!$groups->count()) {
            return false;
        }

        foreach ($groups as $group) {
            if ($this->checkRoute($currentRoute, $group->routes->pluck('route'))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $currentRoute
     * @param $routes
     * @return bool
     */
    protected function checkRoute($currentRoute, $routes): bool
    {
        foreach ($routes as $route) {
            if ($route === '*') {
                return true;
            }

            $selectedRoute = str_replace('*', '', $route);
            $matches = preg_match('/' . $selectedRoute . '/', $currentRoute);

            if ($matches) {
                return true;
            }

            $currentExploded = explode('.', $currentRoute);
            $neededExploded = explode('.', $route);

            if (isset($currentExploded[1]) && isset($neededExploded[1])) {
                $segment = $currentExploded[1] === $neededExploded[1];

                if ($segment && (isset($currentExploded[2]) && isset($neededExploded[2]))) {
                    foreach ($this->getActions($neededExploded[2]) as $r) {
                        if ($currentExploded[2] === $r) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getActions($segment)
    {
        $array = [
            'index'  => [
                'data-table'
            ],
            'create' => [
                'create',
                'store',
                'form',
            ],
            'edit'   => [
                'edit',
                'update',
                'editable',
                'form',
            ]
        ];

        return $array[$segment] ?? [];
    }
}