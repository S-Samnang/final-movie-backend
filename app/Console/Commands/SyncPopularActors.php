<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Actor;

class SyncPopularActors extends Command
{
    protected $signature = 'sync:actors';
    protected $description = 'Sync popular actors from TMDB';

    public function handle()
    {
        $page = 1;
        $maxPages = 3;
        $apiKey = env('TMDB_API_KEY');

        $this->info("🎭 Syncing popular actors from TMDB...");

        while ($page <= $maxPages) {
            $response = Http::get("https://api.themoviedb.org/3/person/popular", [
                'api_key' => $apiKey,
                'language' => 'en-US',
                'page' => $page,
            ]);

            if ($response->failed()) {
                $this->error("❌ Failed to fetch actor data on page {$page}");
                break;
            }

            foreach ($response['results'] as $actor) {
                Actor::updateOrCreate(
                    ['tmdb_id' => $actor['id']],
                    [
                        'name' => $actor['name'],
                        'profile_path' => $actor['profile_path'],
                        'known_for_department' => $actor['known_for_department'],
                        'popularity' => $actor['popularity'],
                    ]
                );
            }

            $this->info("✅ Page {$page} synced.");
            $page++;
        }

        $this->info("✅ All actors synced!");
    }
}