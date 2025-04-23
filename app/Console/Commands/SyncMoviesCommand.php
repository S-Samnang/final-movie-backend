<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Actor;

class SyncMoviesCommand extends Command
{
    protected $signature = 'tmdb:sync {type=top_rated}';
    protected $description = 'Sync movies by type: top_rated, now_playing, upcoming';

    public function handle()
    {
        $type = $this->argument('type');
        $apiKey = config('services.tmdb.api');
        $url = "https://api.themoviedb.org/3/movie/{$type}?api_key={$apiKey}";

        $response = Http::get($url);

        if (!$response->successful()) {
            $this->error('Failed to fetch movies from TMDB.');
            return;
        }

        $movies = $response->json('results');

        foreach ($movies as $movieData) {
            $movie = Movie::updateOrCreate(
                ['tmdb_id' => $movieData['id']],
                [
                    'title' => $movieData['title'],
                    'original_title' => $movieData['original_title'],
                    'release_date' => $movieData['release_date'],
                    'poster_path' => $movieData['poster_path'],
                    'overview' => $movieData['overview'],
                    'vote_average' => $movieData['vote_average'],
                    'type' => $type,
                ]
            );

            // Attach genres
            foreach ($movieData['genre_ids'] as $genreId) {
                $genre = Genre::firstOrCreate(['tmdb_id' => $genreId], [
                    'name' => 'Unknown', // Name will be updated later
                ]);
                $movie->genres()->syncWithoutDetaching([$genre->id]);
            }

            // Fetch cast
            $credits = Http::get("https://api.themoviedb.org/3/movie/{$movieData['id']}/credits", [
                'api_key' => $apiKey,
            ]);

            if ($credits->successful()) {
                $topCast = collect($credits['cast'])->take(10);
                $actorIds = [];

                foreach ($topCast as $person) {
                    $actor = Actor::updateOrCreate(
                        ['tmdb_id' => $person['id']],
                        [
                            'name' => $person['name'],
                            'profile_path' => $person['profile_path'],
                            'known_for_department' => $person['known_for_department'],
                            'popularity' => $person['popularity'],
                        ]
                    );
                    $actorIds[] = $actor->id;
                }

                $movie->actors()->sync($actorIds);
            }
        }

        $this->info("{$type} movies synced successfully.");
    }
}
