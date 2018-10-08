<?php

use Illuminate\Database\Migrations\Migration;
use Laradium\Laradium\Permission\Models\PermissionGroup;
use Laradium\Laradium\Permission\Models\PermissionRole;

class SeedPermissionRolesGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $group = PermissionGroup::create([
            'name' => 'Super powers',
        ]);
        $group->routes()->create([
            'route' => '*'
        ]);

        $role = PermissionRole::create([
            'name'          => 'Global admin',
            'is_superadmin' => 1
        ]);
        $role->groups()->attach($role->id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (PermissionGroup::all() as $group) {
            $group->delete();
        }

        foreach (PermissionRole::all() as $role) {
            $role->delete();
        }
    }
}
