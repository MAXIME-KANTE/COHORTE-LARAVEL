# Utilisation de l'intelligence artificielle — Projet Cohorte

## Phase 0 — Installation et mise en place du dépôt

**Ce que j'ai demandé à l'IA :**  
Aucune demande spécifique pour cette phase d'installation.

**Ce que j'ai retenu :**  
N/A

**Ce que j'ai rejeté et pourquoi :**  
N/A

## Phase 2 — Seeders et factories

**Ce que j'ai demandé à l'IA :**  
Générer les factories Publication et Reponse avec des états par type de publication.

**Ce que j'ai retenu :**  
Les états `question()` et `enModeration()` sur PublicationFactory, qui rendent les tests futurs plus lisibles.

**Ce que j'ai rejeté et pourquoi :**  
Des données de démonstration trop volumineuses : quatre comptes et deux promotions suffisent au correcteur.

## Phase 3 — Authentification Fortify

**Ce que j'ai demandé à l'IA :**  
Installer Laravel Fortify conforme au guide (§7), avec inscription par code d'invitation.

**Ce que j'ai retenu :**  
Le limiteur de connexion personnalisé (5 essais/minute, clé e-mail+IP) et la validation du code d'invitation avec `ValidationException::withMessages` pour des messages en français.

**Ce que j'ai rejeté et pourquoi :**  
La 2FA et les passkeys que `fortify:install` active par défaut : le guide §7.3 les met explicitement hors périmètre, et leurs migrations pollueraient le schéma de base.