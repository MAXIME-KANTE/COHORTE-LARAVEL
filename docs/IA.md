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

## Phase 4 — Adhésion et profil

**Ce que j'ai demandé à l'IA :**  
La structure du middleware ExigePromotion et la gestion des cas limites (enseignant, compte sans promotion).

**Ce que j'ai retenu :**  
La notion d'invariante : vérifier promotion_id une fois en amont pour que tous les scopes en aval le tiennent pour acquis.

**Ce que j'ai rejeté et pourquoi :**  
Rattacher l'enseignant à une promotion via /rejoindre : j'ai préféré un 403 explicite, l'enseignant passe par son propre module.

## Phase 5 — Fil et cloisonnement

**Ce que j'ai demandé à l'IA :**  
Diagnostiquer l'erreur « Call to undefined method middleware() » au premier passage des tests de cloisonnement.

**Ce que j'ai retenu :**  
Depuis Laravel 11, la classe Controller de base du squelette n'étend plus Illuminate\Routing\Controller ; il faut rétablir l'héritage pour utiliser authorizeResource().

**Ce que j'ai rejeté et pourquoi :**  
Remplacer authorizeResource par des $this->authorize() manuels dans chaque méthode : plus verbeux, et le guide valorise la convention resource + policy.

## Phase 6 — Entraide

**Ce que j'ai demandé à l'IA :**  
Le contrôleur de la réponse retenue et la validation du champ reponse_id.

**Ce que j'ai retenu :**  
La faille signalée par le guide : exists:reponses,id ne prouve pas l'appartenance à la question. J'ai ajouté abort_unless($reponse->publication_id === $question->id, 403).

**Ce que j'ai rejeté et pourquoi :**  
Une méthode marquerReponseRetenue() dans QuestionController : le guide recommande un contrôleur dédié à cette action (principe du contrôleur de ressource singleton).