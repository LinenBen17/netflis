<?php

namespace App\Http\Controllers;

use App\Services\TmdbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    public function index()
    {
        $movies = $this->tmdb->getMovies(); // Obtiene las películas populares
        return view('movies.index', compact('movies'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $movies = $this->tmdb->searchMovies($query);
        return view('movies.index', compact('movies'));
    }
}
