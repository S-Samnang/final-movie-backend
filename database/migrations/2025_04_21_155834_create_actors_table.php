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
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->unique();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('profile_path')->nullable();
            $table->string('character')->nullable();
            $table->boolean('adult')->default(false);
            $table->integer('gender')->nullable();
            $table->string('known_for_department')->nullable();
            $table->decimal('popularity', 8, 4)->nullable();
            $table->integer('cast_id')->nullable();
            $table->string('credit_id')->nullable();
            $table->integer('order')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
};
