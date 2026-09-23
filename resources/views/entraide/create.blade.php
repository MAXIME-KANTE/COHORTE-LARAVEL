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
            @forelse ($similaires as $similaire)
                <p>
                    <a href="{{ route('questions.show', $similaire) }}">{{ $similaire->titre }}</a>
                </p>
            @empty
                <p>Aucune question similaire trouvée.</p>
            @endforelse
        </section>
    @endisset
@endsection
