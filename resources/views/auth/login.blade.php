@extends('layouts.app')

@section('titre', 'Connexion')

@section('contenu')
<h1>Se connecter à Cohorte</h1>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <label for="email">Adresse e-mail</label>
    <input id="email" type="email" name="email"
           value="{{ old('email') }}" required autofocus>

    <label for="password">Mot de passe</label>
    <input id="password" type="password" name="password" required>

    <label>
        <input type="checkbox" name="remember"> Se souvenir de moi
    </label>

    <button type="submit">Connexion</button>
</form>

<p>
    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
    —
    <a href="{{ route('register') }}">Créer un compte</a>
</p>
@endsection