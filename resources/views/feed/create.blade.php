@extends('layouts.app')

@section('titre', 'Publier')

@section('contenu')
    <h1>Publier dans le fil</h1>

    <form method="POST" action="{{ route('publications.store') }}">
        @csrf

        <label for="titre">Titre (facultatif)</label>
        <input id="titre" type="text" name="titre" value="{{ old('titre') }}" maxlength="150">
        @error('titre')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <label for="contenu">Votre message</label>
        <textarea id="contenu" name="contenu" rows="6" required minlength="10" maxlength="3000">{{ old('contenu') }}</textarea>
        @error('contenu')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <button type="submit">Publier</button>
    </form>
@endsection
