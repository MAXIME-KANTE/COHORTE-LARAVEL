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
@endsection
