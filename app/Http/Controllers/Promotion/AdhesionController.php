<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdhesionController extends Controller
{
    /**
     * Formulaire de saisie du code d'invitation.
     */
    public function create(): View
    {
        return view('promotion.rejoindre');
    }

    /**
     * Rattache l'utilisateur à la promotion correspondant au code.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->estEnseignant(), 403);

        $donnees = $request->validate([
            'code_invitation' => ['required', 'string', 'max:12'],
        ]);

        $promotion = Promotion::where('code_invitation', $donnees['code_invitation'])
            ->where('ouverte', true)
            ->first();

        if (! $promotion) {
            return back()
                ->withInput()
                ->withErrors(['code_invitation' => 'Code inconnu ou promotion fermée.']);
        }

        $request->user()->update(['promotion_id' => $promotion->id]);

        return redirect()
            ->route('publications.index')
            ->with('succes', 'Bienvenue dans la promotion ' . $promotion->nom . '.');
    }
}
