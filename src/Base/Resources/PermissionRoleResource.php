<?php

namespace Laradium\Laradium\Permission\Base\Resources;

use Illuminate\Http\Request;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Base\Form;
use Laradium\Laradium\Base\Resource;
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
        return laradium()->resource(function (FieldSet $set) {
            $uniqueRule = request()->route()->permission_role ? 'unique:permission_roles,name,' . request()->route()->permission_role : 'unique:permission_roles';
            $set->text('name')->rules('required|min:3|max:255|' . $uniqueRule);

            $set->belongsToMany('groups', 'Groups');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        $resource = $this;
        $slug = $this->getBaseResource()->getSlug();

        $table = laradium()->table(function (ColumnSet $column) use ($resource, $slug) {
            $column->add('name');
            $column->add('groups')->modify(function ($r) {
                $html = '<ul>';

                foreach ($r->groups as $group) {
                    $html .= '<li>' . $group->name . '</li>';
                }

                $html .= '</ul>';

                return $html;
            })->notSortable()->notSearchable();
            $column->add('action')->modify(function ($item) use ($resource, $slug) {
                if ($item->is_superadmin) {
                    return '';
                }

                return view('laradium::admin.resource._partials.action', compact('item', 'resource', 'slug'))->render();
            })->notSortable()->notSearchable();
        });

        return $table;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->model;
        if ($where = $this->resource()->getWhere()) {
            $model = $model->where($where);
        }
        $model = $model->findOrFail($id);

        $this->model($model);

        if ($model->is_superadmin) {
            abort(404);
        }

        $form = (new Form(
            $this
                ->getBaseResource($model)
                ->make($this->resource()->closure())
                ->build())
        )->build();
        $resource = $this;

        return view('laradium::admin.resource.edit', compact('form', 'resource'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     */
    public function update(Request $request, $id)
    {
        $model = $this->model->findOrFail($id);

        if ($model->is_superadmin) {
            abort(404);
        }

        $this->model($model);

        $form = (new Form(
            $this
                ->getBaseResource($model)
                ->make($this->resource()->closure())
                ->build())
        )->build();

        $this->fireEvent('beforeSave', $request);

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $this->saveData($request->all(), $this->getModel());

        $this->fireEvent('afterSave', $request);

        if ($request->ajax()) {
            return [
                'success'  => 'Resource successfully updated!',
                'redirect' => $form->getAction('edit')
            ];
        }

        return back()->withSuccess('Resource successfully updated!');
    }
}