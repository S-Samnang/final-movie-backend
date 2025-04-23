<?php

namespace App\Console\Commands;

use App\Models\Genre;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;

class SyncPopularMovies extends Command
{
    protected $signature = 'sync:popular-movies';
    protected $description = 'Sync popular movies from TMDB into the local database';

    public function handle()
    {
        $page = 1;
        $maxPages = 3;
        $apiKey = env('TMDB_API_KEY');

        $this->info("🎬 Syncing popular movies from TMDB...");

        while ($page <= $maxPages) {
            $response = Http::get("https://api.themoviedb.org/3/movie/popular", [
                'api_key' => $apiKey,
                'language' => 'en-US',
                'page' => $page,
            ]);

            if ($response->failed()) {
                $this->error("❌ Failed to fetch data on page {$page}");
                break;
            }

            foreach ($response['results'] as $movie) {
                $movieModel = Movie::updateOrCreate(
                    ['tmdb_id' => $movie['id']],
                    [
                        'title' => $movie['title'],
                        'original_title' => $movie['original_title'],
                        'overview' => $movie['overview'],
                        'poster_path' => $movie['poster_path'],
                        'backdrop_path' => $movie['backdrop_path'],
                        'release_date' => $movie['release_date'],
                        'original_language' => $movie['original_language'],
                        'adult' => $movie['adult'],
                        'video' => $movie['video'],
                        'vote_average' => $movie['vote_average'],
                        'vote_count' => $movie['vote_count'],
                        'popularity' => $movie['popularity'],
                        'genre_ids' => json_encode($movie['genre_ids']),
                        'type' => 'popular'
                    ]
                );
                $credits = Http::get("https://api.themoviedb.org/3/movie/{$movie['id']}/credits", [
                    'api_key' => $apiKey,
                ]);

                if ($credits->successful()) {
                    $topCast = collect($credits['cast'])->take(10); // limit to top 10 actors

                    $actorIds = [];

                    foreach ($topCast as $person) {
                        $actor = \App\Models\Actor::updateOrCreate(
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

                    // Attach actors to this movie
                    $movieModel->actors()->sync($actorIds);
                }

                if (isset($movie['genre_ids']) && is_array($movie['genre_ids'])) {
                    $genreIds = Genre::whereIn('tmdb_id', $movie['genre_ids'])->pluck('id')->toArray();
                    $movieModel->genres()->sync($genreIds);
                }
            }

            $this->info("✅ Page {$page} synced.");
            $page++;
        }

        $this->info("✅ All popular movies synced successfully.");
    }
}