@extends('layouts.app')

@section('titre', 'Inscription')

@section('contenu')
    <div class="mx-auto mt-10 max-w-md rounded-lg bg-white p-6 shadow">
        <h1 class="mb-4 text-center text-2xl font-semibold">Inscription</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name">Nom</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    autofocus autocomplete="name" class="mt-1 block w-full">
            </div>

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    autocomplete="email" class="mt-1 block w-full">
            </div>

            <div>
                <label for="password">Mot de passe</label>
                <input id="password" type="password" name="password" required
                    autocomplete="new-password" class="mt-1 block w-full">
            </div>

            <div>
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    autocomplete="new-password" class="mt-1 block w-full">
            </div>

            <label for="code_invitation">Code d’invitation de votre promotion</label>
            <input id="code_invitation" type="text" name="code_invitation"
                value="{{ old('code_invitation') }}" required
                placeholder="ex. DWA2026">

            @error('code_invitation')
                <p class="erreur">{{ $message }}</p>
            @enderror

            <button type="submit"
                class="w-full rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
                S'inscrire
            </button>
        </form>

        <p class="mt-4 text-center text-sm">
            Déjà inscrit ?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Se connecter</a>
        </p>
    </div>
@endsection