@props(['publication'])

<article class="carte">
    <h2>
        <a href="{{ route('publications.show', $publication) }}">
            {{ $publication->titre ?? 'Publication' }}
        </a>
        @if ($publication->epingle_le)
            <span class="badge">Épinglé</span>
        @endif
    </h2>
    <p>{{ Str::limit($publication->contenu, 200) }}</p>
    <p class="meta">
        Par {{ $publication->auteur->name }}, {{ $publication->created_at->format('d/m/Y H:i') }}
    </p>
</article>
