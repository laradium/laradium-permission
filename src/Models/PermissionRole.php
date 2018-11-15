<?php

namespace Laradium\Laradium\Permission\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PermissionRole extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'is_superadmin'
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
     * @return array
     */
    public static function getOptions(): array
    {
        return auth()->user()->role->is_superadmin ? self::pluck('name', 'id')->toArray() : self::where('is_superadmin', 0)->pluck('name', 'id')->toArray();
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        $routes = [];

        foreach ($this->groups as $group) {
            foreach ($group->routes as $route) {
                $routes[] = $route->route;
            }
        }

        return array_unique($routes);
    }
}
