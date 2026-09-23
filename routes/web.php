<?php

use Illuminate\Support\Facades\Route;

// La page d'accueil pour tout le monde
Route::get('/', function () {
    return view('accueil');
})->name('accueil');

// Groupe de routes protégées (les fonctionnalités métier viendront ici en Phase 5)
Route::middleware('auth')->group(function () {
    // Pour l'instant, après connexion, Fortify redirigera vers '/'
});
