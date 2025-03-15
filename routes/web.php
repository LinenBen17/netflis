<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MovieController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/movies/save-interest', [MovieController::class, 'saveInterest'])->name('movies.saveInterest');
Route::get('/movies/delete-interest', [MovieController::class, 'deleteInterest'])->name('movies.deleteInterest');
Route::get('/movies/own-interests', [MovieController::class, 'ownInterests'])->name('movies.ownInterests');
