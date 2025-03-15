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
            'language' => 'es-MX',
            'page' => $page,
        ]);

        return $response->json();
    }
    public function getMovieDetails($id)
    {
        $movies = [];

        if (is_array($id)) {
            foreach ($id as $movieId) {
                $response = Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$movieId}", [
                    'api_key' => $this->apiKey,
                    'language' => 'es-MX',
                ]);

                if ($response->successful()) {
                    $movieDetails = $response->json();
                    $movies[] = $movieDetails;
                }
            }
        } else {
            $response = Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$id}", [
                'api_key' => $this->apiKey,
                'language' => 'es-MX',
            ]);

            if ($response->successful()) {
                $movieDetails = $response->json();
                $movies = $movieDetails;
            }
        }

        return $movies;
    }
    public function searchMovies($query)
    {
        $response = Http::withoutVerifying()->get('https://api.themoviedb.org/3/search/movie', [
            'api_key' => $this->apiKey,
            'language' => 'es-MX',
            'query' => $query,
        ]);

        return $response->json();
    }
    /* public function getRecommendations($selectedMovies)
    {
        $recommendations = [];
        $genres = [];

        // Obtener géneros de las películas seleccionadas
        foreach ($selectedMovies as $movieId) {
            $response = Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$movieId}", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES',
            ]);

            if ($response->successful()) {
                $movieDetails = $response->json();
                $genres = array_merge($genres, $movieDetails['genres']);
            }
        }

        // Obtener películas similares basadas en los géneros
        foreach ($genres as $genre) {
            $response = Http::withoutVerifying()->get("https://api.themoviedb.org/3/discover/movie", [
                'api_key' => $this->apiKey,
                'language' => 'es-ES',
                'with_genres' => $genre['id'],
                'sort_by' => 'popularity.desc',
            ]);

            if ($response->successful()) {
                $recommendations = array_merge($recommendations, $response->json()['results']);
            }
        }

        // Eliminar duplicados
        $recommendations = array_unique($recommendations, SORT_REGULAR);

        return array_slice($recommendations, 0, 10);
    } */
    public function getRecommendations($selectedMovies)
    {
        $recommendations = [];

        foreach ($selectedMovies as $movieId) {
            // Hacer una solicitud a la API de TMDb para obtener películas similares
            $response = Http::withoutVerifying()->get("https://api.themoviedb.org/3/movie/{$movieId}/similar", [
                'api_key' => $this->apiKey,
                'language' => 'es-MX',
                'page' => rand(1, 500),
            ]);

            // Añadir las películas similares al array de recomendaciones
            if ($response->successful()) {
                $similarMovies = $response->json()['results'];
                $recommendations = array_merge($recommendations, $similarMovies);
            }
        }

        // Eliminar duplicados (por si hay películas similares repetidas)
        $recommendations = array_unique($recommendations, SORT_REGULAR);

        return $recommendations;
    }

    // Puedes agregar más métodos según las necesidades
}
