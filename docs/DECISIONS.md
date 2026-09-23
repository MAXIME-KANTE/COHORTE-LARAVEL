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
*(À compléter en Phase 8)*

## Décision 5 : Modération hors quota IA
*(À compléter en Phase 9)*
