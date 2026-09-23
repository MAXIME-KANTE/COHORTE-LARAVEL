@extends('layouts.app')

@section('titre', 'Fil de ' . $promotion->nom)

@section('contenu')
    <h1>Fil de la promotion {{ $promotion->nom }}</h1>
    <p>Lecture seule — année {{ $promotion->annee }}</p>

    @forelse ($publications as $publication)
        <article class="carte">
            <h2>{{ $publication->titre ?? 'Publication' }}</h2>
            <p>{{ $publication->contenu }}</p>
            <p>Par {{ $publication->auteur->name }}, {{ $publication->created_at->format('d/m/Y H:i') }}</p>
        </article>
    @empty
        <p>Aucune publication visible dans cette promotion.</p>
    @endforelse

    {{ $publications->links() }}
@endsection
