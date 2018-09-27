<?php

namespace Laradium\Laradium\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermissionRole extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('auth.providers.users.model'))->withTimestamps();
    }

    /**
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class);
    }

    /**
     * @return HasMany
     */
    public function routes(): HasMany
    {
        return $this->hasMany(PermissionRoleRoute::class);
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        $routes = [];

        foreach ($this->groups as $group) {
            foreach ($group->routes as $route) {
                $routes[] = $route->route;
            }
        }

        foreach ($this->routes as $route) {
            $routes[] = $route->route;
        }

        return array_unique($routes);
    }
}
