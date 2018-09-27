<?php

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'namespace'  => 'Laradium\Laradium\Permission\Http\Controllers\Admin',
    'middleware' => ['web', 'laradium'],
], function () {
    Route::get('/access-denied', [
        'uses' => 'PermissionController@denied',
        'as'   => 'access-denied'
    ]);
});