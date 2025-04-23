<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Actor;
use Illuminate\Support\Facades\Http;

class SyncActorsFromMovies extends Command
{
    protected $signature = 'sync:actors-from-movies';
    protected $description = 'Sync actors for each movie in the database using TMDB API';

    public function handle()
    {
        $apiKey = config('services.tmdb.api');
        $movies = Movie::all();
        $bar = $this->output->createProgressBar(count($movies));
        $bar->start();

        foreach ($movies as $movie) {
            $response = Http::get("https://api.themoviedb.org/3/movie/{$movie->tmdb_id}/credits", [
                'api_key' => $apiKey,
            ]);

            if ($response->successful()) {
                $cast = $response->json('cast');

                foreach ($cast as $actorData) {
                    // Create or update actor
                    $actor = Actor::updateOrCreate(
                        ['tmdb_id' => $actorData['id']],
                        [
                            'name' => $actorData['name'],
                            'original_name' => $actorData['original_name'],
                            'character' => $actorData['character'],
                            'known_for_department' => $actorData['known_for_department'],
                            'order' => $actorData['order'],
                            'popularity' => $actorData['popularity'],
                            'gender' => $actorData['gender'],
                            'profile_path' => $actorData['profile_path'],
                            'cast_id' => $actorData['cast_id'] ?? null,
                            'credit_id' => $actorData['credit_id'] ?? null,
                            'adult' => $actorData['adult'] ?? false,
                        ]
                    );

                    // Attach actor to movie (many-to-many)
                    $movie->actors()->syncWithoutDetaching([$actor->id]);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✔ Actors synced successfully for all movies!");
    }
}