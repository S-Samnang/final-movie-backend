<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($movieId)
    {
        $ratings = Rating::with('user')
            ->where('movie_id', $movieId)
            ->latest()
            ->get();

        $avg = Rating::where('movie_id', $movieId)->avg('rating');

        return response()->json([
            'ratings' => $ratings,
            'average' => $avg
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $user = Auth::user();

        Log::info('Authenticated User:', [$user]);

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        $rating = Rating::updateOrCreate(
            ['user_id' => $user->id, 'movie_id' => $request->movie_id],
            [
                'user_id' => $user->id, // ✅ required to create new if not exists
                'movie_id' => $request->movie_id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]
        );

        $rating->load('user');

        return response()->json(['message' => 'Rating submitted', 'data' => $rating]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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