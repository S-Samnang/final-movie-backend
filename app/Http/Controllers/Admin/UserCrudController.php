<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Permission;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn(['name' => 'name', 'label' => 'Name']);
        CRUD::addColumn(['name' => 'email', 'label' => 'Email']);
        CRUD::addColumn([
            'name'      => 'roles',
            'label'     => 'Roles',
            'type'      => 'select_multiple',
            'entity'    => 'roles',
            'attribute' => 'name',
            'model'     => Role::class,
        ]);
        CRUD::column('is_verified')->type('boolean')->label('Verified');
        CRUD::addColumn([
            'name' => 'status',
            'type' => 'boolean',
            'label' => 'Active',
        ]);
    }


    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::addField(['name' => 'name', 'type' => 'text', 'label' => 'Name']);
        CRUD::addField(['name' => 'email', 'type' => 'email', 'label' => 'Email']);

        // Show password field ONLY on create
        CRUD::addField(['name' => 'password', 'type' => 'password', 'label' => 'Password']);

        CRUD::addField([
            'label'     => 'Roles',
            'type'      => 'checklist',
            'name'      => 'roles',
            'entity'    => 'roles',
            'model'     => Role::class,
            'attribute' => 'name',
            'pivot'     => true,
        ]);
        CRUD::addField([
            'name' => 'is_verified',
            'type' => 'boolean',
            'label' => 'Is Verified',
            'default' => false,
        ]);
        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'allows_null' => false,
        ]);
    }
    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::addField(['name' => 'name', 'type' => 'text', 'label' => 'Name']);
        CRUD::addField(['name' => 'email', 'type' => 'email', 'label' => 'Email']);

        // Password field is intentionally NOT added here

        CRUD::addField([
            'label'     => 'Roles',
            'type'      => 'checklist',
            'name'      => 'roles',
            'entity'    => 'roles',
            'model'     => Role::class,
            'attribute' => 'name',
            'pivot'     => true,
        ]);
        CRUD::addField([
            'name' => 'is_verified',
            'type' => 'boolean',
            'label' => 'Is Verified',
            'default' => false,
        ]);
        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'allows_null' => false,
        ]);
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation(); // reuse the same columns for preview
    }

    public function store()
    {
        $this->handlePasswordInput();
        return $this->traitStore();
    }

    public function update()
    {
        $this->handlePasswordInput();
        return $this->traitUpdate();
    }

    protected function handlePasswordInput()
    {
        $request = request();

        if ($request->filled('password')) {
            $request->merge([
                'password' => Hash::make($request->password),
            ]);
        } else {
            $request->request->remove('password');
        }
    }
}