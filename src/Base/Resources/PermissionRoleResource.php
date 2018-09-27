<?php

namespace Laradium\Laradium\Permission\Base\Resources;

use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Permission\Models\PermissionRole;

class PermissionRoleResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = PermissionRole::class;

    /**
     * @return Resource
     */
    public function resource()
    {
        return (new Resource)->make(function (FieldSet $set) {
            $set->text('name')->rules('required|min:3|max:255');

            $set->belongsToMany('groups', 'Groups');
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
            $column->add('groups')->modify(function ($r) {
                $html = '<ul>';

                foreach ($r->groups as $group) {
                    $html .= '<li>' . $group->name . '</li>';
                }

                $html .= '</ul>';

                return $html;
            });
        });
    }
}