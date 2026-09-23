<?php

namespace App\Http\Controllers\Profil;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Profil de l'utilisateur connecté : informations et formulaire
     * de changement de mot de passe portés par Fortify.
     */
    public function show(Request $request): View
    {
        return view('profil.show', ['utilisateur' => $request->user()]);
    }
}
