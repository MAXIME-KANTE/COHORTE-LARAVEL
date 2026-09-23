@extends('layouts.app')

@section('titre', 'File de modération')

@section('contenu')
    <h1>File de modération</h1>
    <p class="meta">
        Publications de votre promotion en attente de décision ou masquées automatiquement.
    </p>

    @forelse ($publications as $publication)
        <article class="carte">
            <h2>
                {{ $publication->titre ?? 'Publication' }}
                <span class="badge">{{ $publication->statut }}</span>
            </h2>
            <p>{{ Str::limit($publication->contenu, 300) }}</p>
            <p class="meta">
                Par {{ $publication->auteur->name }}, le {{ $publication->created_at->format('d/m/Y H:i') }}
                — {{ $publication->signalements_count }} signalement(s)
                @if ($publication->motif_moderation)
                    — motif : {{ $publication->motif_moderation }}
                @endif
            </p>

            <div class="actions">
                <form method="POST" action="{{ route('moderation.update', $publication) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="decision" value="valider">
                    <button type="submit" class="bouton">Valider</button>
                </form>
                <form method="POST" action="{{ route('moderation.update', $publication) }}"
                      onsubmit="return confirm('Refuser définitivement cette publication ?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="decision" value="refuser">
                    <button type="submit" class="bouton bouton--danger">Refuser</button>
                </form>
            </div>
        </article>
    @empty
        <p class="vide">Rien à modérer pour l'instant. Bonne nouvelle.</p>
    @endforelse

    {{ $publications->links() }}
@endsection
