<?php

namespace App\Http\Controllers\Promotion;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Contracts\View\View;

class PromotionController extends Controller
{
    /**
     * Liste des promotions, réservée à l'enseignant.
     */
    public function index(): View
    {
        abort_unless(auth()->user()->estEnseignant(), 403);

        $promotions = Promotion::query()
            ->withCount(['membres', 'publications'])
            ->orderByDesc('annee')
            ->get();

        return view('promotion.index', compact('promotions'));
    }

    /**
     * Fil d'une promotion en lecture seule, réservé à l'enseignant.
     */
    public function show(Promotion $promotion): View
    {
        abort_unless(auth()->user()->estEnseignant(), 403);

        $publications = $promotion->publications()
            ->visibles()
            ->with('auteur')
            ->latest()
            ->paginate(15);

        return view('promotion.show', compact('promotion', 'publications'));
    }
}
