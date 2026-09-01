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