<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Actor;

class SyncActorDetails extends Command
{
    protected $signature = 'sync:actor-details';
    protected $description = 'Fetch and update actor details from TMDB for all actors with a tmdb_id';

    public function handle()
    {
        $apiKey = config('services.tmdb.api_key');

        $actors = Actor::whereNotNull('tmdb_id')->get();
        $this->info("Found {$actors->count()} actors. Starting sync...");

        foreach ($actors as $actor) {
            $this->line("Syncing actor ID {$actor->id} (TMDB: {$actor->tmdb_id})...");

            $url = "https://api.themoviedb.org/3/person/{$actor->tmdb_id}?api_key={$apiKey}&language=en-US";
            $response = Http::get($url);

            if (!$response->successful()) {
                $this->error("❌ Failed to fetch TMDB data for actor {$actor->tmdb_id}");
                continue;
            }

            $data = $response->json();

            $actor->update([
                'name' => $data['name'],
                'original_name' => $data['name'],
                'profile_path' => $data['profile_path'],
                'adult' => $data['adult'],
                'gender' => $data['gender'],
                'known_for_department' => $data['known_for_department'],
                'popularity' => $data['popularity'],
                'also_known_as' => $data['also_known_as'],
                'biography' => $data['biography'],
                'birthday' => $data['birthday'],
                'deathday' => $data['deathday'],
                'homepage' => $data['homepage'],
                'place_of_birth' => $data['place_of_birth'],
                'imdb_id' => $data['imdb_id'],
                'status' => 1,
            ]);

            $this->info("✅ Updated: {$actor->name}");
        }

        $this->info("🎉 All actors synced.");
        return 0;
    }
}