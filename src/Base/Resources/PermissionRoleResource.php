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
            $set->text('name')->rules('required|min:3|max:255');

            $set->belongsToMany('groups', 'Groups');
        });
    }

    /**
     * @return Table
     */
    public function table()
    {
        $resource = $this;
        $slug = $this->getSlug();

        $table = laradium()->table(function (ColumnSet $column) use ($resource, $slug) {
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
            $column->add('action')->modify(function ($item) use ($resource, $slug) {
                if ($item->is_superadmin) {
                    return '';
                }

                return view('laradium::admin.resource._partials.action', compact('item', 'resource', 'slug'))->render();
            });
        });

        return $table;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->model->findOrFail($id);

        if ($model->is_superadmin) {
            abort(404);
        }

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->abstractResource($this);
        $form->buildForm();

        $name = $this->getName();
        $slug = $this->getSlug();

        return view('laradium::admin.resource.edit', compact('form', 'name', 'slug'));
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

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->buildForm();

        if (isset($this->events['beforeSave'])) {
            $this->events['beforeSave']($this->model, $request);
        }

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $model = $this->model->find($id);

        $this->updateResource($request->except('_token'), $model);

        if (isset($this->events['afterSave'])) {
            $this->events['afterSave']($this->model, $request);
        }

        if ($request->ajax()) {
            return [
                'success' => 'Resource successfully updated',
                'data'    => $this->getForm($model->id)
            ];
        }

        return back()->withSuccess('Resource successfully updated!');
    }
}