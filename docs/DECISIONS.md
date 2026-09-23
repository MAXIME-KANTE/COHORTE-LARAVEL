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
*(À compléter en Phase 10)*

## Décision 3 : Dénormalisation du promotion_id
*(À compléter en Phase 1)*

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
