@extends('layouts.app')

@section('titre', 'Accès refusé')

@section('contenu')
    <h1>Accès refusé</h1>
    <p class="alerte alerte--erreur">
        Ce contenu n'appartient pas à votre promotion, ou vous n'avez pas le droit d'effectuer
        cette action.
    </p>
    <p>
        <a href="{{ route('accueil') }}" class="bouton">Retour à l'accueil</a>
    </p>
@endsection
