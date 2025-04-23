<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MovieApiController extends Controller
{
    // Get all movies, optionally filtered by type
    public function index(Request $request)
    {
        $query = Movie::with(['genres', 'actors'])->active();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        return response()->json([
            'movies' => $query->orderByDesc('popularity')->paginate(20),
        ]);
    }

    // Get a single movie by ID
    public function show($id)
    {
        $movie = Movie::with(['genres', 'actors'])->findOrFail($id);
        return response()->json($movie);
    }
    public function showByTmdb($tmdb_id)
    {
        $movie = Movie::with(['genres', 'actors'])
            ->where('tmdb_id', $tmdb_id)
            ->where('status', 1)
            ->first();

        if (!$movie) {
            return response()->json(['message' => 'Movie not found or inactive'], 404);
        }

        return response()->json($movie);
    }
    public function showById($id)
    {
        $movie = \App\Models\Movie::with(['genres', 'actors'])
            ->active()
            ->find($id);

        if (!$movie) {
            return response()->json(['message' => 'Movie not found or inactive'], 404);
        }

        return response()->json($movie);
    }
    // Get the trailer for a movie by TMDB ID
    public function getTrailer($tmdb_id)
    {
        $apiKey = config('services.tmdb.api');
        $response = Http::get("https://api.themoviedb.org/3/movie/{$tmdb_id}/videos", [
            'api_key' => $apiKey,
        ]);

        if (!$response->successful()) {
            return response()->json(['message' => 'Failed to fetch videos'], 500);
        }

        $trailers = collect($response->json('results'));
        $trailer = $trailers->firstWhere(
            fn($video) =>
            $video['type'] === 'Trailer' && $video['site'] === 'YouTube'
        );

        return response()->json([
            'key' => $trailer['key'] ?? null,
            'name' => $trailer['name'] ?? null,
        ]);
    }

    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        $movieId = $request->movie_id;

        if ($user->favoriteMovies()->where('movie_id', $movieId)->exists()) {
            $user->favoriteMovies()->detach($movieId);
            return response()->json(['message' => 'Removed from favorites']);
        }

        $user->favoriteMovies()->attach($movieId);
        return response()->json(['message' => 'Added to favorites']);
    }

    public function getFavorites()
    {
        $user = Auth::user();
        return response()->json($user->favoriteMovies()->with(['genres', 'actors'])->get());
    }
}