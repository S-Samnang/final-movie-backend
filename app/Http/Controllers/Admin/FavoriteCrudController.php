<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FavoriteRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class FavoriteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Favorite::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/favorite');
        CRUD::setEntityNameStrings('favorite', 'favorites');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'label'     => 'User',
            'type'      => 'select',
            'name'      => 'user_id',
            'entity'    => 'user',
            'attribute' => 'name',
            'model'     => "App\Models\User",
        ]);

        CRUD::addColumn([
            'label'     => 'Movie',
            'type'      => 'select',
            'name'      => 'movie_id',
            'entity'    => 'movie',
            'attribute' => 'title',
            'model'     => "App\Models\Movie",
        ]);

        CRUD::column('created_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(FavoriteRequest::class);

        CRUD::addField([
            'label'     => 'User',
            'type'      => 'select2',
            'name'      => 'user_id',
            'entity'    => 'user',
            'attribute' => 'name',
            'model'     => "App\Models\User",
        ]);

        CRUD::addField([
            'label'     => 'Movie',
            'type'      => 'select2',
            'name'      => 'movie_id',
            'entity'    => 'movie',
            'attribute' => 'title',
            'model'     => "App\Models\Movie",
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}