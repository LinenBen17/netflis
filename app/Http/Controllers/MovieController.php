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
        logger($movies);
        return view('movies.index', compact('movies'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $movies = $this->tmdb->searchMovies($query);
        return view('movies.index', compact('movies'));
    }

    public function saveInterest(Request $request)
    {
        $movieInterestID = $request->input('id');
        $movie = $this->tmdb->getMovieDetails($movieInterestID);
        $status = 'error';

        if (!in_array($movie['title'], session('movieInterest'))) {
            session()->push('movieInterest', $movie['title']);
            $status = 'success';
        } else {
            $status = 'repeated';
        }
        if (!in_array($movieInterestID, session('movieInterestID'))) {
            session()->push('movieInterestID', $movieInterestID);
            $status = 'success';
        } else {
            $status = 'repeated';
        }

        logger('movieInterestID', session('movieInterestID'));
        logger('movieInterest', session('movieInterest'));

        return response()->json(['status' => $status]);
    }
}
