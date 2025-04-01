<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_interactions', function (Blueprint $collection) {
            $collection->index('user_id');
            $collection->index('movie_id');
            $collection->index('interaction_type');
            $collection->timestamp('timestamp');

            // Índices correctos para los metadatos
            $collection->index(['user_metadata.username' => 1]);
            $collection->index([
                'movie_metadata.title' => 1,
                'movie_metadata.poster_path' => 1,
                'movie_metadata.overview' => 1,
                'movie_metadata.release_date' => 1,
                'movie_metadata.genres' => 1,
                'movie_metadata.release_year' => 1,
                'movie_metadata.rating' => 1,
                'movie_metadata.popularity' => 1,
                'movie_metadata.vote_count' => 1
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('user_interactions');
    }
};
