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
        <a href="{{ route('feed.index') }}" class="logo">Cohorte</a>
        @auth
            <nav>
                <a href="{{ route('entraide.index') }}">Entraide</a>
                <a href="{{ route('profil.show') }}">{{ auth()->user()->name }}</a>
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