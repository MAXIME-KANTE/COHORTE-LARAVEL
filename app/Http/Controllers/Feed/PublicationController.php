<?php

namespace App\Http\Controllers\Feed;

use App\Enums\VerdictModeration;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePublicationRequest;
use App\Models\Publication;
use App\Services\ServiceModeration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicationController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Publication::class, 'publication');
    }

    public function index(Request $request): View
    {
        $publications = Publication::query()
            ->posts()
            ->visibles()
            ->deLaPromotion($request->user()->promotion_id)
            ->with('auteur')
            ->withCount('signalements')
            ->orderByDesc('epingle_le')
            ->latest()
            ->paginate(15);

        return view('feed.index', compact('publications'));
    }

    public function create(): View
    {
        return view('feed.create');
    }

    public function store(StorePublicationRequest $request, ServiceModeration $moderation): RedirectResponse
    {
        $verdict = $moderation->evaluer($request->validated()['contenu'], $request->user());

        $publication = Publication::create([
            ...$request->validated(),
            'type' => 'post',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => $verdict->statutPublication(),
            'motif_moderation' => $verdict->value,
        ]);

        $message = match ($verdict) {
            VerdictModeration::Acceptable => 'Votre publication est en ligne.',
            VerdictModeration::Inacceptable => 'Votre publication a été refusée par la modération.',
            default => 'Votre publication est en attente de validation par un délégué.',
        };

        return redirect()
            ->route('publications.index')
            ->with($verdict === VerdictModeration::Inacceptable ? 'erreur' : 'succes', $message);
    }

    public function show(Publication $publication): View|RedirectResponse
    {
        // Une question se consulte dans son module d'entraide dédié
        if ($publication->type === 'question') {
            return redirect()->route('questions.show', $publication);
        }

        $publication->load('auteur', 'reponses.auteur');

        $dejaSignale = $publication->signalements()
            ->where('user_id', auth()->id())
            ->exists();

        return view('feed.show', compact('publication', 'dejaSignale'));
    }

    public function destroy(Publication $publication): RedirectResponse
    {
        $publication->delete();

        return redirect()
            ->route('publications.index')
            ->with('succes', 'Publication supprimée.');
    }
}
