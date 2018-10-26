<?php

namespace Laradium\Laradium\Permission\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Permission\Models\PermissionRole;

trait UserPermissions
{

    /**
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(PermissionRole::class);
    }

    /**
     * @param null $resource
     * @param string $action
     * @return bool
     */
    public function hasPermissionTo($resource = null, $action = 'index'): bool
    {
        if ($resource) {
            $currentRoute = $resource instanceof AbstractResource ? $resource->getRoute($action) : (class_exists($resource) ? (new $resource)->getRoute($action) : request()->route()->getName());
        } else {
            $currentRoute = request()->route()->getName();
        }

        if (in_array($currentRoute, $this->allowedRoutes())) {
            return true;
        }

        $role = $this->role;
        if (!$role) {
            return false;
        }

        if ($role->is_superadmin) {
            return true;
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
        // Search for a wildcard route first
        foreach ($routes as $route) {
            if ($route === '*') {
                return true;
            }
        }

        foreach ($routes as $route) {
            $selectedRoute = str_replace('*', '', $route);
            $matches = preg_match('/' . $selectedRoute . '/', $currentRoute);

            if ($matches) {
                return true;
            }

            $currentExploded = explode('.', $currentRoute);
            $neededExploded = explode('.', $route);

            if (isset($currentExploded[1], $neededExploded[1])) {
                $segment = $currentExploded[1] === $neededExploded[1];

                if (isset($currentExploded[2], $neededExploded[2]) && $segment) {
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
     * @param $segment
     * @return array
     */
    protected function getActions($segment): array
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

    /**
     * @return array
     */
    protected function allowedRoutes(): array
    {
        return [
            'admin.access-denied',
            'admin.dashboard',
            'admin.logout'
        ];
    }
}