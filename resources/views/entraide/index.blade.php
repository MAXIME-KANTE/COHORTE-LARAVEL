@extends('layouts.app')

@section('titre', 'Entraide')

@section('contenu')
    <div class="entete-fil">
        <h1>Entraide — {{ auth()->user()->promotion->nom }}</h1>
        <a href="{{ route('questions.create') }}" class="bouton">Poser une question</a>
    </div>

    @forelse ($questions as $question)
        <article class="carte">
            <h2>
                <a href="{{ route('questions.show', $question) }}">{{ $question->titre }}</a>
                @if ($question->reponse_retenue_id)
                    <span class="badge">Réponse retenue</span>
                @endif
            </h2>
            <p>{{ Str::limit($question->contenu, 200) }}</p>
            <p class="meta">
                Par {{ $question->auteur->name }}, {{ $question->created_at->format('d/m/Y H:i') }}
                — {{ $question->reponses_count }} réponse(s)
            </p>
        </article>
    @empty
        <p class="vide">Aucune question pour l'instant. Posez la première !</p>
    @endforelse

    {{ $questions->links() }}
@endsection
