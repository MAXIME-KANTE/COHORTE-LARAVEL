<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titre', 'Cohorte')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a href="#contenu" class="acces-rapide">Aller au contenu</a>

    <header class="barre">
        <a href="{{ route('accueil') }}" class="logo">Cohorte</a>
        @auth
            <nav aria-label="Navigation principale">
                @if (auth()->user()->estEnseignant())
                    <a href="{{ route('enseignant.promotions.index') }}">Promotions</a>
                @else
                    <a href="{{ route('publications.index') }}">Fil</a>
                    <a href="{{ route('questions.index') }}">Entraide</a>
                    @if (auth()->user()->estDelegue())
                        <a href="{{ route('moderation.index') }}">Modération</a>
                    @endif
                    <span class="quota" title="Appels d'assistance IA restants aujourd'hui">
                        Assistance IA : {{ auth()->user()->quotaIaRestant() }} / {{ config('cohorte.quota_ia_quotidien') }}
                    </span>
                    <a href="{{ route('profil.show') }}">{{ auth()->user()->name }}</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Déconnexion</button>
                </form>
            </nav>
        @endauth
    </header>

    <main id="contenu" class="conteneur">
        <x-alerte/>
        @yield('contenu')
    </main>

    <footer class="pied">
        <p>Cohorte — le réseau social de votre promotion</p>
    </footer>
</body>
</html>