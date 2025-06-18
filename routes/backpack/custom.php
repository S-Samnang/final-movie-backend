<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\CRUD.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('actor', 'ActorCrudController');
    Route::crud('movie', 'MovieCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('permission', 'PermissionCrudController');

    Route::get('movie/sync-actors/{id}', [\App\Http\Controllers\Admin\MovieCrudController::class, 'syncActors']);
    
    Route::get('dashboard', function () {
        return view('vendor.backpack.base.dashboard');
    });
    Route::crud('rating', 'RatingCrudController');
    Route::crud('favorite', 'FavoriteCrudController');
}); // this should be the absolute last line of this file

/**
 * DO NOT ADD ANYTHING HERE.
 */
