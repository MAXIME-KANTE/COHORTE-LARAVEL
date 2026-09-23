<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use App\Models\Reponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReponseController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Publication $question): RedirectResponse
    {
        $this->authorize('view', $question);

        $donnees = $request->validate([
            'contenu' => ['required', 'string', 'min:2', 'max:2000'],
        ], [
            'contenu.required' => 'Votre réponse ne peut pas être vide.',
        ]);

        $question->reponses()->create([
            'user_id' => $request->user()->id,
            'contenu' => $donnees['contenu'],
        ]);

        return back()->with('succes', 'Votre réponse est publiée.');
    }

    public function destroy(Reponse $reponse): RedirectResponse
    {
        $this->authorize('delete', $reponse);

        $reponse->delete();

        return back()->with('succes', 'Réponse supprimée.');
    }
}
