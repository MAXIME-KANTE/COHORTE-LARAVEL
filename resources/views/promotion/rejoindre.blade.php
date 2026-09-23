@extends('layouts.app')

@section('titre', 'Rejoindre une promotion')

@section('contenu')
    <h1>Rejoindre une promotion</h1>
    <p>Saisissez le code d'invitation remis par votre formateur pour accéder au fil de votre promotion.</p>

    <form method="POST" action="{{ route('promotion.adherer') }}">
        @csrf

        <label for="code_invitation">Code d'invitation</label>
        <input id="code_invitation" type="text" name="code_invitation" value="{{ old('code_invitation') }}" required maxlength="12" autofocus>
        @error('code_invitation')
            <p class="erreur">{{ $message }}</p>
        @enderror

        <button type="submit">Rejoindre</button>
    </form>
@endsection
