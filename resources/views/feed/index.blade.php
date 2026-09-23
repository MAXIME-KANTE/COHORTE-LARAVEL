@extends('layouts.app')

@section('titre', 'Fil de la promotion')

@section('contenu')
    <div class="entete-fil">
        <h1>{{ auth()->user()->promotion->nom }}</h1>
        <a href="{{ route('publications.create') }}" class="bouton">Publier</a>
    </div>

    @forelse ($publications as $publication)
        <x-carte-publication :publication="$publication" />
    @empty
        <p class="vide">Aucune publication pour l'instant. Lancez la conversation.</p>
    @endforelse

    {{ $publications->links() }}
@endsection
