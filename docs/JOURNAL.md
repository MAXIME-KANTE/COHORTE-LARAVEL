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

## Phase 2 — Seeders et factories

**Branche :** feat/02-seeders
**Date :** 3 septembre 2026 (entrée consignée au journal lors de la phase 3)

### Ce que j'ai fait
J'ai créé les factories Promotion, Publication et Reponse (avec les états `question()` et `enModeration()`) puis le DatabaseSeeder qui initialise deux promotions (GRPA26, GRPB26) et quatre comptes de démonstration documentés dans le README.

### Pourquoi je l'ai fait ainsi
Les comptes de démonstration permettent au correcteur de tester le cloisonnement entre promotions sans créer de données à la main, et les états de factory rendent les tests lisibles.

### La difficulté rencontrée
La répartition des rôles (apprenant, délégué, enseignant) devait être réaliste : un enseignant sans promotion, un délégué dans la promotion A.

### Comment je l'ai résolue
J'ai fixé la liste des comptes avant d'écrire le seeder et je les ai documentés dans le README pour ne jamais m'en écarter.

## Phase 3 — Authentification Fortify avec code d'invitation

**Branche :** feat/03-authentification-fortify
**Date :** 23 septembre 2026

### Ce que j'ai fait
J'ai installé Laravel Fortify et restreint son périmètre aux fonctionnalités du sujet : inscription, connexion, réinitialisation de mot de passe, mise à jour du profil et du mot de passe. L'inscription exige un code d'invitation valide et rattache l'utilisateur à sa promotion avec le rôle apprenant. J'ai aussi complété le schéma des tables métier (publications, reponses, signalements, appels_ia) livré vide à la phase 1, ajouté la page d'accueil publique et un limiteur de tentatives de connexion (5 par minute et par e-mail/IP).

### Pourquoi je l'ai fait ainsi
Fortify gère les routes d'authentification sans controller boilerplate, et le guide §7.3 liste exactement les fonctionnalités attendues : tout le reste est hors périmètre. La validation du code d'invitation dans CreateNewUser avec des messages explicites (code inconnu, inscriptions closes) donne un retour clair à l'apprenant.

### La difficulté rencontrée
Deux problèmes : `fortify:install` active par défaut la 2FA et les passkeys, explicitement hors périmètre (§7.3), avec deux migrations et des colonnes users inutiles ; et les migrations métier de la phase 1 étaient des fichiers vides que les modèles référençaient déjà.

### Comment je l'ai résolue
J'ai retiré les fonctionnalités 2FA/passkeys de la config, supprimé leurs migrations avant tout commit, et complété les cinq migrations vides dans un commit `fix(bdd)` à part, validé par `migrate:fresh --seed` et des tests d'inscription (code valide, code inconnu, promotion fermée).

## Phase 4 — Rejoindre une promotion et le profil

**Branche :** feat/04-adhesion-promotion
**Date :** 23 septembre 2026

### Ce que j'ai fait
J'ai créé le middleware ExigePromotion (alias `promotion`) qui garantit qu'aucun membre sans promotion n'entre dans les pages métier, le module d'adhésion par code d'invitation (AdhesionController), le tableau de bord enseignant qui liste toutes les promotions avec leurs compteurs et lit leur fil, ainsi que la page de profil branchée sur les formulaires de mise à jour Fortify.

### Pourquoi je l'ai fait ainsi
C'est une invariante : le middleware vérifie une fois, en amont, que promotion_id existe, et tout le code en aval (scopes deLaPromotion, etc.) peut le tenir pour acquis. L'enseignant est redirigé vers son propre module car il n'a jamais de promotion_id.

### La difficulté rencontrée
La route `publications.index` n'existe qu'à partir de la phase 5, alors que l'adhésion et Fortify redirigent déjà vers elle ; et l'enseignant qui posterait un code sur /rejoindre serait rattaché à une promotion, ce qui n'a pas de sens.

### Comment je l'ai résolue
J'ai posé une route `/publications` temporaire (remplacée par le resource controller en phase 5) pour que les redirections restent valides à chaque merge, et un `abort_if(enseignant, 403)` défensif dans AdhesionController@store.

## Phase 5 — Le fil de promotion et le cloisonnement

**Branche :** feat/05-fil-promotion
**Date :** 23 septembre 2026

### Ce que j'ai fait
J'ai créé PublicationPolicy, le FormRequest StorePublicationRequest et le contrôleur de ressource Feed/PublicationController (index/create/store/show/destroy), avec le fil paginé trié par épinglage, les vues feed et le composant carte-publication. La route temporaire /publications de la phase 4 a été remplacée par le resource controller dans le groupe auth+promotion.

### Pourquoi je l'ai fait ainsi
Deux mécanismes se combinent : authorizeResource protège chaque page de détail via la policy, et le scope deLaPromotion filtre la liste. Protéger l'un sans l'autre laisse la porte ouverte — c'est le cœur du cloisonnement noté sur 15 points.

### La difficulté rencontrée
`authorizeResource()` levait « Call to undefined method middleware() » : sous Laravel 13, le Controller de base du squelette n'étend plus Illuminate\Routing\Controller, donc les middlewares de contrôleur n'existaient plus.

### Comment je l'ai résolue
J'ai fait hériter app/Http/Controllers\Controller de Illuminate\Routing\Controller (commit fix dédié). Le test du guide §9.7 passe : fatou (groupe B) reçoit un 403 en accédant directement à /publications/{id} d'une publication du groupe A, et l'auteur voit sa propre publication masquée.