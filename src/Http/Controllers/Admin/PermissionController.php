<?php

namespace Laradium\Laradium\Permission\Http\Controllers\Admin;

class PermissionController
{

    /**
     * @return mixed
     */
    public function denied()
    {
        $routes = auth()->user()->role->getRoutes();

        return view('laradium-permission::access-denied', compact('routes'));
    }
}
