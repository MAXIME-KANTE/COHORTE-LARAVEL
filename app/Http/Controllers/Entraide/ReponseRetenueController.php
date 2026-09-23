<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Reponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReponseRetenueController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Publication $question): RedirectResponse
    {
        $this->authorize('designerReponse', $question);

        $donnees = $request->validate([
            'reponse_id' => ['required', 'integer', 'exists:reponses,id'],
        ]);

        $reponse = Reponse::findOrFail($donnees['reponse_id']);

        $question->update(['reponse_retenue_id' => $reponse->id]);
        $reponse->auteur->increment('points', 10);

        return back()->with('succes', 'Réponse retenue. Merci pour votre contribution.');
    }
}
