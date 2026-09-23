@extends('layouts.app')

@section('titre', 'Mon profil')

@section('contenu')
    <h1>Mon profil</h1>

    <section class="carte">
        <h2>Informations</h2>
        <p>
            {{ $utilisateur->name }} — {{ $utilisateur->email }}<br>
            @if ($utilisateur->promotion)
                Promotion : {{ $utilisateur->promotion->nom }} (rôle : {{ $utilisateur->role }})
            @else
                Aucune promotion (rôle : {{ $utilisateur->role }})
            @endif
        </p>

        <form method="POST" action="{{ route('user-profile-information.update') }}">
            @csrf
            @method('PUT')

            <label for="name">Nom</label>
            <input id="name" type="text" name="name" value="{{ old('name', $utilisateur->name) }}" required>

            <label for="email">Adresse e-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $utilisateur->email) }}" required>

            <button type="submit">Mettre à jour</button>
        </form>
    </section>

    <section class="carte">
        <h2>Changer de mot de passe</h2>

        <form method="POST" action="{{ route('user-password.update') }}">
            @csrf
            @method('PUT')

            <label for="current_password">Mot de passe actuel</label>
            <input id="current_password" type="password" name="current_password" required>

            <label for="password">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">

            <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

            <button type="submit">Changer le mot de passe</button>
        </form>
    </section>
@endsection
