<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actors', function (Blueprint $table) {
            $table->text('also_known_as')->nullable();
        $table->text('biography')->nullable();
        $table->string('birthday')->nullable();
        $table->string('deathday')->nullable();
        $table->string('homepage')->nullable();
        $table->string('place_of_birth')->nullable();
        $table->string('imdb_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actors', function (Blueprint $table) {
            //
        });
    }
};