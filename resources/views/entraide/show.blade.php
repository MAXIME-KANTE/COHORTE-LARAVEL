@extends('layouts.app')

@section('titre', $question->titre)

@section('contenu')
    <p><a href="{{ route('questions.index') }}">← Retour à l'entraide</a></p>

    <article class="carte">
        <h1>{{ $question->titre }}</h1>
        <p>{{ $question->contenu }}</p>
        <p class="meta">
            Par {{ $question->auteur->name }}, {{ $question->created_at->format('d/m/Y H:i') }}
        </p>

        @can('signaler', $question)
            @unless ($dejaSignale)
                <details class="signalement">
                    <summary>Signaler cette question</summary>
                    <form method="POST" action="{{ route('signalements.store', $question) }}">
                        @csrf
                        <label for="motif">Motif du signalement</label>
                        <select id="motif" name="motif" required>
                            <option value="insulte">Insulte ou propos injurieux</option>
                            <option value="hors_sujet">Hors sujet</option>
                            <option value="publicite">Publicité</option>
                            <option value="autre">Autre</option>
                        </select>
                        <button type="submit" class="bouton bouton--danger">Signaler</button>
                    </form>
                </details>
            @else
                <p class="meta">Vous avez déjà signalé cette question.</p>
            @endunless
        @endcan
    </article>

    <h2>{{ $question->reponses->count() }} réponse(s)</h2>

    @forelse ($question->reponses as $reponse)
        <article class="carte @if ($question->reponse_retenue_id === $reponse->id) carte--retenue @endif">
            <p>{{ $reponse->contenu }}</p>
            <p class="meta">
                Par {{ $reponse->auteur->name }}
                @if ($question->reponse_retenue_id === $reponse->id)
                    — <strong>réponse retenue</strong>
                @endif
            </p>

            @can('designerReponse', $question)
                @unless ($question->reponse_retenue_id)
                    <form method="POST" action="{{ route('reponse-retenue.store', $question) }}">
                        @csrf
                        <input type="hidden" name="reponse_id" value="{{ $reponse->id }}">
                        <button type="submit" class="bouton">Retenir cette réponse</button>
                    </form>
                @endunless
            @endcan

            @can('delete', $reponse)
                <form method="POST" action="{{ route('reponses.destroy', $reponse) }}"
                      onsubmit="return confirm('Supprimer cette réponse ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bouton bouton--danger">Supprimer</button>
                </form>
            @endcan
        </article>
    @empty
        <p class="vide">Aucune réponse pour l'instant.</p>
    @endforelse

    <section class="carte">
        <h2>Répondre</h2>
        <form method="POST" action="{{ route('reponses.store', $question) }}">
            @csrf

            <label for="contenu">Votre réponse</label>
            <textarea id="contenu" name="contenu" rows="4" required minlength="2" maxlength="2000">{{ old('contenu') }}</textarea>
            @error('contenu')
                <p class="erreur">{{ $message }}</p>
            @enderror

            <button type="submit">Publier la réponse</button>
        </form>
    </section>
@endsection
