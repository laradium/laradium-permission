<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_role_routes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('permission_role_id');
            $table->foreign('permission_role_id')->references('id')->on('permission_roles')->onDelete('cascade');
            $table->string('route')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_role_routes');
    }
}
