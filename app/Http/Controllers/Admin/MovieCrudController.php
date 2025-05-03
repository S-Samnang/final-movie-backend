<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\MovieRequest;
use App\Models\Actor;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Http;
use Prologue\Alerts\Facades\Alert;

/**
 * Class MovieCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class MovieCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Movie::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/movie');
        CRUD::setEntityNameStrings('movie', 'movies');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('title');
        CRUD::column('release_date')->label('Release Date');
        CRUD::column('vote_average')->label('Rating');
        CRUD::column('popularity');

        CRUD::addColumn([
            'name' => 'poster_path',
            'type' => 'image',
            'label' => 'Poster',
            'prefix' => 'https://image.tmdb.org/t/p/w185',
            'height' => '60px',
            'width' => '40px',
        ]);
        CRUD::addColumn([
            'label'     => 'Genres',
            'type'      => 'select_multiple',
            'name'      => 'genres', // relation name
            'entity'    => 'genres',
            'attribute' => 'name',
            'model'     => \App\Models\Genre::class,
        ]);
        CRUD::addColumn([
            'label'     => 'Actors',
            'type'      => 'select_multiple',
            'name'      => 'actors', // relationship name in Movie model
            'entity'    => 'actors',
            'attribute' => 'name',
            'model'     => \App\Models\Actor::class,
        ]);
        CRUD::column('type');
        CRUD::addColumn([
            'name' => 'status',
            'type' => 'boolean',
            'label' => 'Active',
        ]);

        $this->crud->addButtonFromView('line', 'sync_actors', 'sync_actors', 'beginning');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(MovieRequest::class);

        CRUD::field('tmdb_id')->type('number')->label('TMDB ID');
        CRUD::field('type')->type('text')->label('Type (upcoming, now_playing, popular)');
        CRUD::field('title')->type('text')->label('Title');
        CRUD::field('original_title')->type('text')->label('Original Title');
        CRUD::field('overview')->type('textarea')->label('Overview');
        CRUD::field('poster_path')->type('text')->label('Poster Path (TMDB)');
        CRUD::field('backdrop_path')->type('text')->label('Backdrop Path (TMDB)');
        CRUD::field('release_date')->type('date')->label('Release Date');
        CRUD::field('original_language')->type('text')->label('Original Language');
        CRUD::field('adult')->type('checkbox')->label('Adult');
        CRUD::field('video')->type('checkbox')->label('Has Video?');
        CRUD::field('vote_average')->type('number')->label('Vote Average')->attributes(['step' => '0.1']);
        CRUD::field('vote_count')->type('number')->label('Vote Count');
        CRUD::field('popularity')->type('number')->label('Popularity')->attributes(['step' => '0.0001']);

        CRUD::addField([
            'label'     => 'Genres',
            'type'      => 'checklist',
            'name'      => 'genres',
            'entity'    => 'genres',
            'attribute' => 'name',
            'model'     => \App\Models\Genre::class,
            'pivot'     => true,
        ]);
        CRUD::addField([
            'name'  => 'embed_url',
            'label' => 'Embed URL (iframe or video)',
            'type'  => 'textarea',
        ]);
        CRUD::addField([
            'name' => 'status',
            'label' => 'Status',
            'type' => 'select_from_array',
            'options' => [1 => 'Active', 0 => 'Inactive'],
            'allows_null' => false,
        ]);

        // CRUD::addField([
        //     'label'     => 'Actors',
        //     'type'      => 'select_multiple',
        //     'name'      => 'actors',
        //     'entity'    => 'actors',
        //     'attribute' => 'name',
        //     'model'     => \App\Models\Actor::class,
        //     'pivot'     => true,
        // ]);


        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }


    public function syncActors($id)
    {
        $movie = \App\Models\Movie::findOrFail($id);
        $tmdbId = $movie->tmdb_id;
        $apiKey = config('services.tmdb.api');

        $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdbId}/credits", [
            'api_key' => $apiKey,
        ]);

        if (!$response->successful()) {
            Alert::error('Failed to fetch actors from TMDB')->flash();
            return back();
        }

        $cast = $response->json('cast');

        foreach ($cast as $actorData) {
            $actor = Actor::updateOrCreate(
                ['tmdb_id' => $actorData['id']],
                [
                    'name' => $actorData['name'],
                    'original_name' => $actorData['original_name'],
                    'profile_path' => $actorData['profile_path'],
                    'character' => $actorData['character'],
                    'adult' => $actorData['adult'],
                    'gender' => $actorData['gender'],
                    'known_for_department' => $actorData['known_for_department'],
                    'popularity' => $actorData['popularity'],
                    'cast_id' => $actorData['cast_id'] ?? null,
                    'credit_id' => $actorData['credit_id'] ?? null,
                    'order' => $actorData['order'] ?? null,
                ]
            );

            $movie->actors()->syncWithoutDetaching([$actor->id]);
        }

        Alert::success("✅ Synced actors for '{$movie->title}' from TMDB!")->flash();
        return redirect()->back();
    }
}
