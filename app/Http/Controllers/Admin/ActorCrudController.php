<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ActorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ActorCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Actor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/actor');
        CRUD::setEntityNameStrings('actor', 'actors');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('original_name');
        CRUD::column('character');
        CRUD::addColumn([
            'name' => 'profile_path',
            'label' => 'Photo',
            'type' => 'image',
            'prefix' => 'https://image.tmdb.org/t/p/w185',
            'height' => '60px',
            'width' => '60px',
        ]);
        CRUD::column('popularity');
        CRUD::column('gender');
        CRUD::column('known_for_department')->label('Department');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(ActorRequest::class);

        CRUD::field('tmdb_id')->type('number')->label('TMDB ID');
        CRUD::field('name')->type('text');
        CRUD::field('original_name')->type('text');
        CRUD::field('profile_path')->type('text')->label('Profile Image Path');
        CRUD::field('character')->type('text');
        CRUD::field('adult')->type('checkbox');
        CRUD::field('gender')->type('select_from_array')->options([
            0 => 'Not specified',
            1 => 'Female',
            2 => 'Male',
        ]);
        CRUD::field('known_for_department')->type('text')->label('Known For Department');
        CRUD::field('popularity')->type('number')->attributes(['step' => '0.0001']);
        CRUD::field('cast_id')->type('number');
        CRUD::field('credit_id')->type('text');
        CRUD::field('order')->type('number');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}