<?php

namespace App\Http\Controllers\Entraide;

use App\Enums\VerdictModeration;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Publication;
use App\Services\ServiceModeration;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuestionController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Publication::class, 'question');
    }

    public function index(Request $request): View
    {
        $questions = Publication::query()
            ->questions()
            ->visibles()
            ->deLaPromotion($request->user()->promotion_id)
            ->with('auteur', 'reponseRetenue.auteur')
            ->withCount('reponses')
            ->latest()
            ->paginate(15);

        return view('entraide.index', compact('questions'));
    }

    public function create(): View
    {
        return view('entraide.create');
    }

    public function store(StoreQuestionRequest $request, ServiceModeration $moderation): RedirectResponse
    {
        $verdict = $moderation->evaluer($request->validated()['contenu'], $request->user());

        $question = Publication::create([
            ...$request->validated(),
            'type' => 'question',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => $verdict->statutPublication(),
            'motif_moderation' => $verdict->value,
        ]);

        $message = match ($verdict) {
            VerdictModeration::Acceptable => 'Votre question est en ligne.',
            VerdictModeration::Inacceptable => 'Votre question a été refusée par la modération.',
            default => 'Votre question est en attente de validation par un délégué.',
        };

        return redirect()
            ->route('questions.index')
            ->with($verdict === VerdictModeration::Inacceptable ? 'erreur' : 'succes', $message);
    }

    public function show(Publication $question): View
    {
        $question->load('auteur', 'reponses.auteur', 'reponseRetenue.auteur');

        return view('entraide.show', compact('question'));
    }
}
