<?php

namespace Laradium\Laradium\Permission\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionRoleRoute extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['route', 'permission_role_id'];
}
