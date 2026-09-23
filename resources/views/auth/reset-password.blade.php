@extends('layouts.app')

@section('titre', 'Nouveau mot de passe')

@section('contenu')
    <h1>Choisir un nouveau mot de passe</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">Adresse e-mail</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required>
        @error('email')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <label for="password">Nouveau mot de passe</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <label for="password_confirmation">Confirmer le mot de passe</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

        <button type="submit">Réinitialiser</button>
    </form>
@endsection
