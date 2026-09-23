<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Services\ServiceDetectionDoublon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DetectionDoublonController extends Controller
{
    public function store(Request $request, ServiceDetectionDoublon $detection): RedirectResponse
    {
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:150'],
            'contenu' => ['nullable', 'string'],
        ]);

        $similaires = $detection->chercherSimilaires($donnees['titre'], $request->user());

        if ($similaires === []) {
            return redirect()
                ->route('questions.create')
                ->withInput()
                ->with('succes', 'Aucune question similaire trouvée dans votre promotion.');
        }

        return redirect()
            ->route('questions.create')
            ->withInput()
            ->with('similaires', $similaires);
    }
}
