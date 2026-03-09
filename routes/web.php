<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\TeamController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/players/create', [App\Http\Controllers\PlayerController::class, 'create'])->name('players.create');
    Route::post('/players', [App\Http\Controllers\PlayerController::class, 'store'])->name('players.store');
    Route::get('/players/{player}/edit', [App\Http\Controllers\PlayerController::class, 'edit'])->name('players.edit');
    Route::put('/players/{player}', [App\Http\Controllers\PlayerController::class, 'update'])->name('players.update');
    Route::delete('/players/{player}', [App\Http\Controllers\PlayerController::class, 'destroy'])->name('players.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes publiques
Route::get('/', [App\Http\Controllers\PlayerController::class, 'index'])->name('home');
Route::get('/players', [App\Http\Controllers\PlayerController::class, 'index'])->name('players.index');
Route::get('/players/{player}', [App\Http\Controllers\PlayerController::class, 'show'])->name('players.show');

// Routes authentifiées pour les teams
Route::middleware('auth')->group(function () {
    Route::resource('teams', App\Http\Controllers\TeamController::class);
    Route::post('/teams/{team}/players', [App\Http\Controllers\TeamController::class, 'addPlayer'])->name('teams.players.add');
    Route::delete('/teams/{team}/players/{player}', [App\Http\Controllers\TeamController::class, 'removePlayer'])->name('teams.players.remove');
});


Route::middleware('auth')->group(function () {
    Route::get('/teams/{team}/formation', [App\Http\Controllers\TeamController::class, 'formation'])->name('teams.formation');
    Route::post('/teams/{team}/formation', [App\Http\Controllers\TeamController::class, 'saveFormation'])->name('teams.formation.save');
});


require __DIR__ . '/auth.php';
