<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Movie;

class MovieSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json(['results' => []]);
        }

        $results = Movie::where('title', 'like', '%' . $query . '%')
            ->with('genres') // optional
            ->take(20)
            ->get();

        return response()->json(['results' => $results]);
    }
}
