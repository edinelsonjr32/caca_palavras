<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\UserProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rota principal: Redireciona para o login se não estiver logado, ou para o dashboard se estiver.
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('index');
})->name('index');

// Rotas de Autenticação para convidados (não logados)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Rotas Protegidas (Apenas para usuários logados)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/avatar', [UserProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    // Rotas do Jogo
    Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
    Route::get('/game/play/{level}', [GameController::class, 'play'])->name('game.play');
    Route::post('/game/validate', [GameController::class, 'validateWord'])->name('game.validate');
    Route::get('/game/end', [GameController::class, 'end'])->name('game.end');

    // Rota do Ranking
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking');
});
