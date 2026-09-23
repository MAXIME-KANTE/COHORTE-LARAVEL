@extends('layouts.app')

@section('titre', 'Mes promotions')

@section('contenu')
    <h1>Mes promotions</h1>

    @forelse ($promotions as $promotion)
        <article class="carte">
            <h2>
                <a href="{{ route('enseignant.promotions.show', $promotion) }}">{{ $promotion->nom }}</a>
            </h2>
            <p>
                Année {{ $promotion->annee }} —
                {{ $promotion->membres_count }} membre{{ $promotion->membres_count > 1 ? 's' : '' }},
                {{ $promotion->publications_count }} publication{{ $promotion->publications_count > 1 ? 's' : '' }}
            </p>
            <p>Code d'invitation : <code>{{ $promotion->ouverte ? $promotion->code_invitation : 'inscriptions closes' }}</code></p>
        </article>
    @empty
        <p>Aucune promotion pour le moment.</p>
    @endforelse
@endsection
