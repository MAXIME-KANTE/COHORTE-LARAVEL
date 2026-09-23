@extends('layouts.app')

@section('titre', 'Poser une question')

@section('contenu')
    <h1>Poser une question à la promotion</h1>

    <form method="POST" action="{{ route('questions.store') }}">
        @csrf

        <label for="titre">Titre</label>
        <input id="titre" type="text" name="titre" value="{{ old('titre') }}" maxlength="150" required>
        @error('titre')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <label for="contenu">Détail de la question</label>
        <textarea id="contenu" name="contenu" rows="6" required minlength="10" maxlength="3000">{{ old('contenu') }}</textarea>
        @error('contenu')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <button type="submit">Publier la question</button>
    </form>

    @isset($similaires)
        <section class="carte">
            <h2>Questions similaires déjà posées</h2>
            <p>Votre question ressemble à celles-ci. Consultez-les peut-être avant de publier :</p>
            <ul>
                @forelse ($similaires as $similaire)
                    <li>
                        <a href="{{ route('questions.show', $similaire) }}">{{ $similaire->titre }}</a>
                    </li>
                @empty
                    <li>Aucune question similaire trouvée.</li>
                @endforelse
            </ul>

            <form method="POST" action="{{ route('questions.store') }}">
                @csrf
                <input type="hidden" name="titre" value="{{ old('titre') }}">
                <input type="hidden" name="contenu" value="{{ old('contenu') }}">
                <input type="hidden" name="doublon_verifie" value="1">
                <button type="submit" class="bouton">Publier quand même</button>
            </form>
        </section>
    @endisset
@endsection
