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
            $uniqueRule = request()->route()->permission_group ? 'unique:permission_groups,name,' . request()->route()->permission_group : 'unique:permission_groups';
            $set->text('name')->rules('required|min:3|max:255|' . $uniqueRule);

            $set->hasMany('routes')->fields(function (FieldSet $set) {
                $set->select('route')->options((new PermissionGroup)->getRoutes());
            })->entryLabel('route');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        $table = laradium()->table(function (ColumnSet $column) {
            $column->add('name');
            $column->add('routes')->modify(function ($r) {
                $html = '<ul>';

                foreach ($r->routes as $route) {
                    $html .= '<li>' . $route->route . '</li>';
                }

                $html .= '</ul>';

                return $html;
            })->notSearchable()->notSortable();
        });

        return $table;
    }
}