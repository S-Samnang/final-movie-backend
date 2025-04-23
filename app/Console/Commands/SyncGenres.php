<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Genre;

class SyncGenres extends Command
{
    protected $signature = 'sync:genres';
    protected $description = 'Sync genres from TMDB into the local database';

    public function handle()
    {
        $apiKey = env('TMDB_API_KEY');

        $this->info("🎟️ Fetching genres from TMDB...");

        $response = Http::get('https://api.themoviedb.org/3/genre/movie/list', [
            'api_key' => $apiKey,
            'language' => 'en-US',
        ]);

        if ($response->failed()) {
            $this->error("❌ Failed to fetch genres.");
            return;
        }

        foreach ($response['genres'] as $genre) {
            Genre::updateOrCreate(
                ['tmdb_id' => $genre['id']],
                ['name' => $genre['name']]
            );
        }

        $this->info("✅ Genres synced successfully!");
    }
}