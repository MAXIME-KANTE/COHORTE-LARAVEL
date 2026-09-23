<?php

use App\Http\Controllers\Profil\ProfilController;
use App\Http\Controllers\Promotion\AdhesionController;
use App\Http\Controllers\Promotion\PromotionController;
use Illuminate\Support\Facades\Route;

// La page d'accueil pour tout le monde
Route::get('/', function () {
    return view('accueil');
})->name('accueil');

// Groupe de routes protégées
Route::middleware('auth')->group(function () {
    // Adhésion à une promotion par code d'invitation
    Route::get('/rejoindre', [AdhesionController::class, 'create'])->name('promotion.rejoindre');
    Route::post('/rejoindre', [AdhesionController::class, 'store'])->name('promotion.adherer');

    // Tableau de bord enseignant (lecture seule sur toutes les promotions)
    Route::get('/promotions', [PromotionController::class, 'index'])->name('enseignant.promotions.index');
    Route::get('/promotions/{promotion}', [PromotionController::class, 'show'])
        ->scopeBindings()->name('enseignant.promotions.show');

    // Profil de l'utilisateur connecté
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');

    // Temporaire : remplacé par le fil complet en phase 5
    Route::get('/publications', fn () => view('accueil'))
        ->middleware('promotion')->name('publications.index');
});
