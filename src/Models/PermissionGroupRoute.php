<?php

namespace Laradium\Laradium\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroupRoute extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['route', 'permission_group_id'];
}
