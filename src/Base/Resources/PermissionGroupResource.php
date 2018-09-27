<?php

namespace Laradium\Laradium\Permission\Base\Resources;

use Illuminate\Support\Facades\Route;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Permission\Models\PermissionGroup;

class PermissionGroupResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = PermissionGroup::class;

    /**
     * @return Resource
     */
    public function resource()
    {
        return (new Resource)->make(function (FieldSet $set) {
            $routes = [
                '*'            => '*',
                '*.index'      => '*.index',
                '*.data-table' => '*.data-table',
                '*.create'     => '*.create',
                '*.store'      => '*.store',
                '*.edit'       => '*.edit',
                '*.update'     => '*.update',
                '*.destroy'    => '*.destroy',
            ];

            foreach (Route::getRoutes() as $route) {
                if ($route->getName()) {
                    $exploded = explode('.', $route->getName());
                    if (isset($exploded[0])) {
                        if ($exploded[0] !== 'admin') {
                            continue;
                        }

                        $partial = $exploded[0] . '.' . $exploded[1] . '.*';
                        $routes[$partial] = $partial;
                        $routes[$route->getName()] = $route->getName();
                    }
                }
            }

            $set->text('name')->rules('required|min:3|max:255');

            $set->hasMany('routes')->fields(function (FieldSet $set) use ($routes) {
                $set->select('route')->options($routes);
            });
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        return (new Table)->make(function (ColumnSet $column) {
            $column->add('id', '#ID');
            $column->add('name');
            $column->add('routes')->modify(function ($r) {
                $html = '<ul>';

                foreach ($r->routes as $route) {
                    $html .= '<li>' . $route->route . '</li>';
                }

                $html .= '</ul>';

                return $html;
            });
        });
    }
}