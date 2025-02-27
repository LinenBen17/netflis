<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    public function getMovies($page = 1)
    {
        $response = Http::withoutVerifying()->get('https://api.themoviedb.org/3/movie/popular', [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'page' => $page,
        ]);

        return $response->json();
    }

    public function searchMovies($query)
    {
        $response = Http::withoutVerifying()->get('https://api.themoviedb.org/3/search/movie', [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'query' => $query,
        ]);

        return $response->json();
    }

    // Puedes agregar más métodos según las necesidades
}
