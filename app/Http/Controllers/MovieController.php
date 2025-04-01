<?php

namespace App\Http\Controllers;

use App\Models\UserInteractions;
use App\Services\MovieRecommenderService;
use App\Services\netflisApiService;
use App\Services\TmdbService;
use Carbon\Carbon;
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

    private function getUniqueUserId($username)
    {
        $sessionUserId = session('unique_user_id');

        // Si ya hay un user_id en la sesión, verificar si coincide con el username
        if ($sessionUserId) {
            $existingUser = UserInteractions::where('user_id', $sessionUserId)->first();
            if ($existingUser && $existingUser->user_metadata['username'] === $username) {
                return $sessionUserId; // Reusar si coincide
            }
        }

        // Generar un nuevo user_id único
        $newUserId = $username . '-' . \Illuminate\Support\Str::uuid();
        session(['unique_user_id' => $newUserId]);
        return $newUserId;
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
        $genres = [];
        $status = 'error';

        $username = session('userSession', 'Guest');
        $userId = $this->getUniqueUserId($username);

        if (!UserInteractions::where('user_id', $userId)
            ->where('movie_id', $movie['id'])
            ->where('interaction_type', 'interested')
            ->exists()) {
            if (!in_array($movieInterestID, session('movieInterestID', []))) {
                session()->push('movieInterestID', intval($movieInterestID));
            }

            foreach ($movie['genres'] as $genre) {
                array_push($genres, $genre['name']);
            }

            UserInteractions::create([
                'user_id' => $userId,
                'movie_id' => $movie['id'],
                'interaction_type' => 'interested',
                'timestamp' => Carbon::now(),
                'user_metadata' => ['username' => $username],
                'movie_metadata' => [
                    'title' => $movie['title'],
                    'poster_path' => $movie['poster_path'],
                    'overview' => $movie['overview'],
                    'release_date' => $movie['release_date'],
                    'genres' => $genres,
                    'release_year' => Carbon::parse($movie['release_date'])->format("Y"),
                    'rating' => $movie['vote_average'],
                    'popularity' => $movie['popularity'],
                    'vote_count' => $movie['vote_count'],
                ]
            ]);
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
        $movieId = $request['id'];
        $moviesInterestID = session('movieInterestID', []);

        $username = session('userSession', 'Guest');
        $userId = $this->getUniqueUserId($username);

        $movie = array_diff($moviesInterestID, array($movieId));
        session(['movieInterestID' => array_values($movie)]);

        UserInteractions::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->delete();

        return response()->json(['status' => 'success']);
    }

    public function getPopularMovies()
    {
        $popularMovies = UserInteractions::raw(function ($collection) {
            return $collection->aggregate([
                ['$match' => ['interaction_type' => 'interested']],
                ['$group' => [
                    '_id' => '$movie_id',
                    'count' => ['$sum' => 1],
                    'title' => ['$first' => '$movie_metadata.title'],
                    'poster_path' => ['$first' => '$movie_metadata.poster_path'],
                    'overview' => ['$first' => '$movie_metadata.overview'],
                    'release_date' => ['$first' => '$movie_metadata.release_date'],
                    'genres' => ['$first' => '$movie_metadata.genres'],
                    'rating' => ['$first' => '$movie_metadata.rating'],
                    'popularity' => ['$first' => '$movie_metadata.popularity'],
                ]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 10]
            ]);
        });

        return view('movies.global-interests', compact('popularMovies'));
    }

    public function getStatistics()
    {
        $username = session('userSession', 'Guest');
        $userId = $this->getUniqueUserId($username);

        $userStats = UserInteractions::raw(function ($collection) use ($userId) {
            return $collection->aggregate([
                ['$match' => ['user_id' => $userId, 'interaction_type' => 'interested']],
                ['$group' => [
                    '_id' => null,
                    'total_interests' => ['$sum' => 1],
                    'genres' => ['$push' => '$movie_metadata.genres']
                ]],
                ['$project' => [
                    'total_interests' => 1,
                    'genres' => ['$reduce' => [
                        'input' => '$genres',
                        'initialValue' => [],
                        'in' => ['$concatArrays' => ['$$value', '$$this']]
                    ]]
                ]]
            ])->toArray();
        });

        $userGenres = [];
        if (!empty($userStats)) {
            $userGenresFlat = $userStats[0]['genres']->bsonSerialize();
            $userGenresCount = array_count_values($userGenresFlat);
            arsort($userGenresCount);
            $userGenres = array_slice($userGenresCount, 0, 5, true);
        }

        $globalGenresStats = UserInteractions::raw(function ($collection) {
            return $collection->aggregate([
                ['$match' => ['interaction_type' => 'interested']],
                ['$unwind' => '$movie_metadata.genres'],
                ['$group' => [
                    '_id' => '$movie_metadata.genres',
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 5]
            ])->toArray();
        });

        $topUsers = UserInteractions::raw(function ($collection) {
            return $collection->aggregate([
                ['$match' => ['interaction_type' => 'interested']],
                ['$group' => [
                    '_id' => [
                        'user_id' => '$user_id',
                        'username' => '$user_metadata.username'
                    ],
                    'count' => ['$sum' => 1]
                ]],
                ['$sort' => ['count' => -1]],
                ['$limit' => 5]
            ])->toArray();
        });

        return view('movies.statistics', compact(
            'userStats',
            'userGenres',
            'globalGenresStats',
            'topUsers'
        ));
    }
}
