<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Fetch the actor
        $actor = Actor::find($id);

        if (!$actor) {
            return response()->json([
                'error' => true,
                'message' => 'Actor not found.'
            ], 404);
        }

        // Fetch movies the actor has casted in
        $movies = $actor->movies()
            ->select('movies.id', 'title', 'poster_path', 'release_date', 'tmdb_id')
            ->get();

        return response()->json([
            'error' => false,
            'actor' => [
                'id' => $actor->id,
                'name' => $actor->name,
                'profile_path' => $actor->profile_path,
                'gender' => $actor->gender,
                'known_for_department' => $actor->known_for_department,
                'movies' => $movies
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
