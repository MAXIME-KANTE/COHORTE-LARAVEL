## Phase 0 — Installation et mise en place du dépôt

**Branche :** feat/00-installation  
**Date :** 31 août 2026

### Ce que j'ai fait
J'ai créé un nouveau projet Laravel 12, configuré une base de données SQLite, initialisé le dépôt Git, et mis en place la configuration métier centralisée ainsi que le gabarit de base.

### Pourquoi je l'ai fait ainsi
Le choix de SQLite simplifie l'installation pour le correcteur (aucun serveur à configurer). La configuration centralisée dans `config/cohorte.php` permet de modifier les seuils métier sans toucher au code.

### La difficulté rencontrée
Aucune difficulté majeure, mais j'ai dû vérifier que le fichier `.env` ne soit pas commité par erreur.

### Comment je l'ai résolue
J'ai vérifié manuellement le fichier `.gitignore` et créé un fichier `.env.example` propre.

## Phase 1 — Le modèle de données et les relations Eloquent

**Branche :** feat/01-modele-donnees  
**Dates :** 1er septembre 2026

### Ce que j'ai fait

J'ai créé les six tables de la base de données (promotions, publications, reponses, signalements, appels_ia) et enrichi la table users. J'ai déclaré toutes les relations Eloquent entre les modèles et ajouté les scopes pour filtrer par promotion et par type. J'ai activé preventLazyLoading pour détecter les problèmes N+1.

### Pourquoi je l'ai fait ainsi

J'ai choisi de dénormaliser promotion_id sur les publications pour éviter les jointures dans les requêtes de cloisonnement. Les scopes centralisent la logique de filtrage, ce qui facilitera les modifications futures. Les relations nommées en français (auteur, membres) rendent le code des vues plus lisible.

### La difficulté rencontrée

L'ordre des migrations était crucial : promotions doit exister avant users, et reponses doit exister avant d'ajouter reponse_retenue_id. J'ai dû renommer certains fichiers de migration pour respecter l'ordre chronologique.

### Comment je l'ai résolue

J'ai vérifié l'ordre avec `Get-ChildItem database/migrations/` et utilisé Rename-Item pour ajuster les timestamps. J'ai testé avec `php artisan migrate:fresh` pour valider que tout fonctionne.