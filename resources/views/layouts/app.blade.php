<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Cohorte')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="barre">
        <a href="/" class="logo">Cohorte</a>
        @auth
            <nav>
                @if (auth()->user()->estEnseignant())
                    <a href="{{ route('enseignant.promotions.index') }}">Promotions</a>
                @else
                    <a href="{{ route('publications.index') }}">Fil</a>
                    <a href="{{ route('questions.index') }}">Entraide</a>
                    <a href="{{ route('profil.show') }}">{{ auth()->user()->name }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </nav>
        @endauth
    </header>

    <main class="conteneur">
        <x-alerte/>
        @yield('contenu')
    </main>
</body>
</html>