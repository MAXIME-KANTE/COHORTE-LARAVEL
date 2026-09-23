@extends('layouts.app')

@section('titre', 'Bienvenue sur Cohorte')

@section('contenu')
    <h1>Cohorte — le réseau social de votre promotion</h1>
    <p>On n'y entre que sur invitation. Publiez, posez vos questions, entraidez-vous.</p>

    @guest
        <a href="{{ route('register') }}" class="bouton">Créer un compte</a>
        <a href="{{ route('login') }}" class="bouton">Se connecter</a>
    @endguest
@endsection
