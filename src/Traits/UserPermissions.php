<?php

namespace Laradium\Laradium\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return bool
     */
    public function hasPermission(Request $request): bool
    {
        $currentRoute = $request->route()->getName();
        if ($currentRoute === 'laradium::permissions.access-denied') {
            return true;
        }

        $role = $this->role;
        if (!$role) {
            return false;
        }

        foreach ($role->routes as $route) {
            if ($this->checkRoute($currentRoute, $route)) {
                return true;
            }
        }

        $groups = $role->groups;
        if (!$groups->count()) {
            return false;
        }

        foreach ($groups as $group) {
            foreach ($group->routes as $route) {
                if ($this->checkRoute($currentRoute, $route)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param $currentRoute
     * @param $route
     * @return bool
     */
    protected function checkRoute($currentRoute, $route): bool
    {
        $selectedRoute = str_replace('*', '', $route->route);
        $matches = preg_match('/' . $selectedRoute . '/', $currentRoute);

        return $route->route === '*' || $matches;
    }
}