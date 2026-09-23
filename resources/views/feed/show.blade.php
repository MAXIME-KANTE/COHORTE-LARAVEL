@extends('layouts.app')

@section('titre', $publication->titre ?? 'Publication')

@section('contenu')
    <p><a href="{{ route('publications.index') }}">← Retour au fil</a></p>

    <article class="carte">
        <h1>
            {{ $publication->titre ?? 'Publication' }}
            @if ($publication->epingle_le)
                <span class="badge">Épinglé</span>
            @endif
        </h1>
        <p>{{ $publication->contenu }}</p>
        <p class="meta">
            Par {{ $publication->auteur->name }}, {{ $publication->created_at->format('d/m/Y H:i') }}
        </p>
    </article>

    @if ($publication->statut !== 'publie' && $publication->user_id === auth()->id())
        <div class="alerte alerte--erreur">
            Votre publication est {{ $publication->statut === 'masque' ? 'masquée' : 'en attente de modération' }}
            @if ($publication->motif_moderation)
                (motif : {{ $publication->motif_moderation }})
            @endif
            . Elle reste visible par vous uniquement.
        </div>
    @endif

    @can('delete', $publication)
        <form method="POST" action="{{ route('publications.destroy', $publication) }}"
              onsubmit="return confirm('Supprimer cette publication ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bouton bouton--danger">Supprimer</button>
        </form>
    @endcan

    @can('signaler', $publication)
        @unless ($dejaSignale)
            <details class="signalement">
                <summary>Signaler cette publication</summary>
                <form method="POST" action="{{ route('signalements.store', $publication) }}">
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
            <p class="meta">Vous avez déjà signalé cette publication.</p>
        @endunless
    @endcan
@endsection
