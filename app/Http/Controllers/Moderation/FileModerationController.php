<?php

namespace App\Http\Controllers\Moderation;

use App\Http\Controllers\Controller;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FileModerationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->estDelegue(), 403);

        $publications = Publication::query()
            ->deLaPromotion($request->user()->promotion_id)
            ->whereIn('statut', ['en_moderation', 'masque'])
            ->with('auteur')
            ->withCount('signalements')
            ->latest()
            ->paginate(20);

        return view('moderation.index', compact('publications'));
    }

    public function update(Request $request, Publication $publication): RedirectResponse
    {
        // Être délégué ne suffit pas : il faut l'être de la promotion concernée
        abort_unless($request->user()->estDelegue(), 403);
        abort_unless($request->user()->promotion_id === $publication->promotion_id, 403);

        $donnees = $request->validate([
            'decision' => ['required', 'in:valider,refuser'],
        ]);

        $publication->update([
            'statut' => $donnees['decision'] === 'valider' ? 'publie' : 'refuse',
        ]);

        return back()->with('succes', 'Décision enregistrée.');
    }
}
