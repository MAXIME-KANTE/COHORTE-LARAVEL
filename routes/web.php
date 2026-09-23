<?php

use App\Http\Controllers\Feed\PublicationController;
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
});

// Le fil de la promotion, réservé aux membres rattachés
Route::middleware(['auth', 'promotion'])->group(function () {
    Route::resource('publications', PublicationController::class)
        ->only(['index', 'create', 'store', 'show', 'destroy']);
});
