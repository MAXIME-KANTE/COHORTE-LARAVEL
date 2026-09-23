<?php

namespace App\Http\Controllers\Entraide;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreQuestionRequest;
use App\Models\Publication;
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

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $question = Publication::create([
            ...$request->validated(),
            'type' => 'question',
            'user_id' => $request->user()->id,
            'promotion_id' => $request->user()->promotion_id,
            'statut' => 'publie',
        ]);

        return redirect()
            ->route('questions.show', $question)
            ->with('succes', 'Votre question est en ligne.');
    }

    public function show(Publication $question): View
    {
        $question->load('auteur', 'reponses.auteur', 'reponseRetenue.auteur');

        return view('entraide.show', compact('question'));
    }
}
