# Décisions architecturales — Projet Cohorte

## Décision 1 : Panne d'OpenRouter — fail-open ou fail-closed ?

**Statut :** retenue en phase 7

**Contexte.** Chaque publication passe par un modèle de langage avant d'entrer dans le fil. Si OpenRouter est en panne (timeout, 503, quota épuisé), deux conduites sont possibles : publier quand même (fail-open) ou mettre la publication en file de modération humaine (fail-closed).

**Choix : fail-closed.** Lorsque le service est indisponible, la publication reçoit le statut `en_moderation` et un délégué la tranche. Rien n'entre dans le fil sans avoir été contrôlé par une machine ou un humain.

**Pourquoi.** Cohorte est un réseau interne à une école : le public est scolaire et le volume de publications reste faible (une promotion, pas des millions d'utilisateurs). La file de modération humaine reste tenable en cas de panne longue, alors qu'un contenu problématique publié sans contrôle pendant une panne serait, lui, irrattrapable — les apprenants l'auraient déjà vu. On privilégie la sécurité sur la disponibilité.

**Alternative écartée : fail-open.** Elle garde l'application fluide en cas de panne, mais accepte qu'un contenu inacceptable transite sans aucun contrôle. Le risque est disproportionné pour le gain dans ce contexte.

**Revers de la décision.** Si la panne dure, le délégué peut se retrouver avec beaucoup de publications à trier et les membres ont l'impression que l'application est cassée. C'est le prix assumé du contrôle systématique.

**Configurable.** Le comportement est piloté par `COHORTE_MODERATION_FAIL_OPEN` dans `.env` (`false` par défaut). Le passage en fail-open est donc un changement de configuration, pas de code.

## Décision 2 : Stockage ou recalcul de la réputation

**Statut :** retenue en phase 10

**Contexte.** Le score de contribution doit apparaître sur le profil et dans le gabarit (droit d'épingler). Deux implémentations possibles : une colonne `points` maintenue par incréments, ou un calcul d'agrégation à chaque affichage.

**Choix : stockage incrémental + commande de recalcul.** Les événements créditent la colonne `users.points` au moment où ils surviennent (réponse retenue : `+10` via increment), et `php artisan cohorte:recalculer-reputation` sait recalculer tout le monde à partir des données réelles (réponses retenues ×10 + réponses ×3 + questions ×1 − publications refusées ×5, plancher à 0).

**Pourquoi.** Le stockage est la seule option tenable pour un affichage dans le gabarit de chaque page : recalculer à chaque rendu coûterait une agrégation par page vue. Mais un incrément oublié désynchroniserait silencieusement le compteur — d'où la commande qui fait foi, exécutable en cron ou après un correctif.

**Alternative écartée : le recalcul à l'affichage.** Toujours juste, mais une requête d'agrégation sur publications + reponses à chaque page, pour chaque membre connecté : disproportionné.

## Décision 3 : Dénormalisation du promotion_id

**Statut :** actée dès la phase 1, formalisée ici

**Contexte.** Une publication pourrait retrouver sa promotion via son auteur (users.promotion_id) au lieu de porter sa propre colonne publications.promotion_id.

**Choix : promotion_id figure directement sur publications** (clé étrangère indexée, avec un index composite promotion_id + statut + created_at).

**Pourquoi.** La requête de cloisonnement — celle qui filtre le fil de promotion — devient une simple condition sur une colonne indexée, sans jointure : c'est la requête la plus exécutée de l'application. Et une publication reste rattachée à la promotion dans laquelle elle a été écrite même si son auteur change de promotion ensuite : l'historique d'une promotion ne suit pas les personnes.

**Alternative écartée : déduire la promotion par la jointure users.** Elle économise une colonne au prix d'une jointure sur chaque lecture du fil et d'un historique instable. Aucun des deux n'est acceptable ici.

## Décision 4 : Seuil de signalement

**Statut :** retenue en phase 8

**Contexte.** À partir de combien de signalements distincts une publication est-elle automatiquement masquée du fil ? Le seuil s'applique à des promotions d'une trentaine de membres.

**Choix : 3 signalements distincts.** Au troisième signalement, la publication passe au statut `masque` et disparaît du fil (elle reste visible pour son auteur, accompagnée du motif).

**Pourquoi.** Trois signalements provenant de trois membres différents, c'est déjà un signal fort dans une promotion de cette taille : ce n'est probablement pas un accident ni un acharnement isolé. En dessous (1 ou 2), un couple de membres en désaccord pourrait faire taire n'importe qui.

**Alternative écartée : un seuil à 5 ou plus.** Il rendrait le masquage quasi inatteignable dans une promotion calme, et la règle métier existerait sur le papier seulement.

**Revers de la décision.** Trois membres coordonnés peuvent masquer une publication saine. C'est pour cela que la publication n'est pas supprimée et que le délégué peut la restaurer depuis sa file de modération : le masquage est réversible par un humain.

**Configurable.** `COHORTE_SEUIL_SIGNALEMENT` dans `.env` (3 par défaut).

## Décision 5 : Modération hors quota IA

**Statut :** retenue en phase 9

**Contexte.** Deux fonctionnalités consomment l'IA : la modération automatique (chaque publication) et la détection de doublon (chaque question). Le quota quotidien de 10 appels est commun. À qui l'appliquer ?

**Choix : seul le détecteur de doublon est soumis au quota.** La modération s'exécute toujours, même quota épuisé ; le middleware `quota.ia` ne protège que la route `questions/verifier-doublon`, et le flow de création retombe silencieusement sur l'enregistrement direct quand le quota est vide.

**Pourquoi.** La modération est une contrainte que l'application impose à ses membres, pas un service qu'elle leur rend : la refuser reviendrait à empêcher un membre de s'exprimer (ou à publier sans contrôle). La détection de doublon, elle, est une assistance : on peut la retirer sans dommage — le membre publie simplement sans vérification.

**Alternative écartée : appliquer le quota aux deux.** Elle économiserait des appels, mais créerait deux effets absurdes : des publications non modérées passant dans le fil en fin de journée, et des membres privés de publication pour avoir trop questionné l'IA.

**Les deux comptent.** Chaque appel (modération compris) écrit sa ligne dans appels_ias : le quota affiché reflète bien la consommation totale du budget quotidien.
