@extends('layouts.app')

@section('titre', 'Mot de passe oublié')

@section('contenu')
    <h1>Mot de passe oublié</h1>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <button type="submit">Envoyer le lien</button>
    </form>

    <p><a href="{{ route('login') }}">Retour à la connexion</a></p>
@endsection
