<?php

namespace App\Http\Controllers;

use App\Services\MovieRecommenderService;
use App\Services\netflisApiService;
use App\Services\TmdbService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $tmdb;
    protected $netflisApiService;

    public function __construct(TmdbService $tmdb, netflisApiService $netflisApiService)
    {
        $this->tmdb = $tmdb;
        $this->netflisApiService = $netflisApiService;
    }

    public function index()
    {
        $page = 1;
        $movies = $this->tmdb->getMovies($page);
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

        if (!in_array($movie['title'], session('movieInterest', []))) {
            session()->push('movieInterest', $movie['title']);
            $status = 'success';
        } else {
            $status = 'repeated';
        }
        if (!in_array($movieInterestID, session('movieInterestID', []))) {
            session()->push('movieInterestID', intval($movieInterestID));
            $status = 'success';
        } else {
            $status = 'repeated';
        }

        return response()->json(['status' => $status]);
    }

    public function ownInterests()
    {
        $moviesInterested = [];
        $recommendations = [];
        $recommendationsID = [];
        $invalidIds = [];
        $movieInterestID = session('movieInterestID');

        if ($movieInterestID) {
            // Obtener detalles de las películas seleccionadas
            $moviesInterested = $this->tmdb->getMovieDetails($movieInterestID);
            // Obtener recomendaciones
            $recommendations = $this->netflisApiService->recommend($movieInterestID);
        }
        if (isset($recommendations['invalid_ids'])) {
            $invalidIds = $recommendations['invalid_ids'];

            if (isset($recommendations['recomendaciones'])) {
                foreach ($recommendations['recomendaciones'] as $recommendation) {
                    $recommendationsID[] = $recommendation['id'];
                }
                $recommendations = $this->tmdb->getMovieDetails($recommendationsID);
            }

            return view('movies.own-interests', compact('moviesInterested', 'recommendations', 'invalidIds'));
        }

        foreach ($recommendations as $recommendation) {
            $recommendationsID[] = $recommendation['id'];
        }

        $recommendations = $this->tmdb->getMovieDetails($recommendationsID);

        return view('movies.own-interests', compact('moviesInterested', 'recommendations'));
    }

    public function deleteInterest(Request $request)
    {
        logger("deleteInterest");

        logger($request['id']);

        $movieId = $request['id'];
        $moviesInterestID = session('movieInterestID', []);

        logger($moviesInterestID);

        $movie = array_diff($moviesInterestID, array($movieId));

        session(['movieInterestID' => array_values($movie)]);


        return response()->json(['status' => 'success']);
    }
}
