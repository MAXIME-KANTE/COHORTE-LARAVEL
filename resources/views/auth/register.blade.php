@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <section class="carte carte--formulaire">
        <h1>Créer un compte</h1>
        <p class="sous-titre">Rejoignez la promotion de votre école avec votre code d'invitation.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <label for="name">Nom</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                autofocus autocomplete="name">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                autocomplete="email">

            <label for="password">Mot de passe</label>
            <input id="password" type="password" name="password" required
                autocomplete="new-password">

            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password">

            <label for="code_invitation">Code d’invitation de votre promotion</label>
            <input id="code_invitation" type="text" name="code_invitation"
                value="{{ old('code_invitation') }}" required
                placeholder="ex. DWA2026">

            @error('code_invitation')
                <p class="erreur">{{ $message }}</p>
            @enderror

            <button type="submit" class="bouton bouton--pleine">S'inscrire</button>
        </form>

        <p class="pied-formulaire">
            Déjà inscrit ?
            <a href="{{ route('login') }}">Se connecter</a>
        </p>
    </section>
@endsection