<?php

namespace Database\Seeders;

use App\Models\UserInteractions;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserInteractionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserInteractions::create([
            'user_id' => 'session_abc123',
            'movie_id' => 'tt1375666',
            'interaction_type' => 'interested',
            'timestamp' => Carbon::now(),
            'user_metadata' => [
                'username' => 'JuanPerez'
            ],
            'movie_metadata' => [
                'title' => 'Inception',
                'poster_path' => '/edKpE9B5qN3e559OuMCLZdW1iBZ.jpg',
                'overview' => 'Unlikely hero Mickey Barnes finds himself in the extraordinary circumstance of working for an employer who demands the ultimate commitment to the job… to die, for a living.',
                'release_date' => '2025-02-28',
                'genres' => ['Science Fiction', 'Action', 'Thriller'],
                'release_year' => 2010,
                'rating' => 8.8,
                'popularity' => 95.2,
                'vote_count' => 35000
            ]
        ]);
    }
}
