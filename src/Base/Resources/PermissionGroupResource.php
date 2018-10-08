<?php

namespace Laradium\Laradium\Permission\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
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
        return laradium()->resource(function (FieldSet $set) {
            $set->text('name')->rules('required|min:3|max:255');

            $set->hasMany('routes')->fields(function (FieldSet $set) {
                $set->select('route')->options((new PermissionGroup)->getRoutes());
            });
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        $table = laradium()->table(function (ColumnSet $column) {
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

        return $table;
    }
}