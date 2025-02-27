<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TmdbService;
use App\Models\Movie;

class FetchMovies extends Command
{
    protected $signature = 'movies:fetch';
    protected $description = 'Obtiene las películas populares desde TMDb y las guarda en la base de datos';

    protected $tmdbService;

    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    public function handle()
    {
        $this->info('Obteniendo películas populares desde TMDb...');

        $moviesData = $this->tmdbService->getMovies(); // Usa el servicio para obtener las películas

        if (isset($moviesData['results'])) {
            foreach ($moviesData['results'] as $movie) {
                Movie::updateOrCreate(
                    ['tmdb_id' => $movie['id']], // Evita duplicados usando el ID de TMDb
                    [
                        'title' => $movie['title'],
                        'overview' => $movie['overview'],
                        'poster_path' => $movie['poster_path'],
                        'release_date' => $movie['release_date'],
                        'vote_average' => $movie['vote_average'],
                        'vote_count' => $movie['vote_count']
                    ]
                );
            }

            $this->info('Películas guardadas/actualizadas correctamente.');
        } else {
            $this->error('No se pudieron obtener películas. Verifica tu API Key y conexión.');
        }
    }
}
