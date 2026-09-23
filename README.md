# Cohorte — le réseau social de promotion, propulsé par l'IA

Application Laravel 12 : un réseau privé où l'on n'entre que sur invitation, où l'on ne voit
que le contenu de sa promotion, et où chaque publication est modérée par un modèle de langage
(OpenRouter) avant d'entrer dans le fil.

## Prérequis

- PHP 8.2 ou supérieur (extensions classiques de Laravel : pdo_sqlite, mbstring, openssl…)
- Composer
- Une clé API OpenRouter gratuite (https://openrouter.ai — aucun compte bancaire requis
  tant que vous utilisez un modèle `:free`)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configurez ensuite le `.env` :

```dotenv
# Base de données : SQLite par défaut, aucun serveur à installer
DB_CONNECTION=sqlite

# Modération IA
OPENROUTER_API_KEY=sk-or-v1-votre-cle
OPENROUTER_MODEL=z-ai/glm-5.2:free
OPENROUTER_TIMEOUT=12
```

> **Modèle utilisé pendant le projet :** `z-ai/glm-5.2:free` (catalogue gratuit d'OpenRouter,
> septembre 2026). Le catalogue des modèles gratuits change souvent : si la modération échoue,
> choisissez un autre identifiant `:free` sur https://openrouter.ai/models et mettez à jour
> `OPENROUTER_MODEL`.

Puis créez la base et les comptes de démonstration :

```bash
php artisan migrate:fresh --seed
php artisan serve
```

L'application est disponible sur http://localhost:8000.

## Comptes de démonstration

Quatre comptes sont créés automatiquement par le seeder (mot de passe : `password`) :

| Nom | Email | Mot de passe | Rôle | Promotion |
|-----|-------|--------------|------|-----------|
| Awa Diop | awa@cohorte.test | password | Apprenant | Groupe A (code `GRPA26`) |
| Moussa Ba | moussa@cohorte.test | password | Délégué | Groupe A (code `GRPA26`) |
| Fatou Sow | fatou@cohorte.test | password | Apprenant | Groupe B (code `GRPB26`) |
| Formateur | formateur@cohorte.test | password | Enseignant | Aucune |

**Important :** ces comptes sont obligatoires pour la soutenance. Le correcteur testera le
cloisonnement en se connectant avec Awa puis en essayant d'accéder à une publication de Fatou
(résultat attendu : erreur 403).

## Les règles métier

1. **Cloisonnement** — un apprenant ne voit que sa promotion, y compris en tapant une URL à la main (403).
2. **Quota IA** — 10 appels par membre et par jour, remis à zéro à minuit (fuseau `Africa/Dakar`).
   Au-delà, l'assistance se désactive proprement ; publier reste toujours possible.
3. **Modération communautaire** — 3 signalements distincts masquent une publication
   (visible par son auteur, avec motif ; le délégué peut la restaurer).
4. **Réputation** — réponse retenue +10, réponse +3, question +1, publication refusée −5.
   À 50 points, le membre peut épingler une publication en haut du fil.

Les seuils sont configurables via `config/cohorte.php` (`COHORTE_QUOTA_IA`,
`COHORTE_SEUIL_SIGNALEMENT`, `COHORTE_SEUIL_EPINGLAGE`, `COHORTE_MODERATION_FAIL_OPEN`).

## Commandes utiles

```bash
php artisan cohorte:recalculer-reputation   # remet les scores de contribution d'aplomb
php artisan test                            # suite de tests complète
```

## Documentation

- `docs/JOURNAL.md` — journal de bord, une entrée par phase
- `docs/DECISIONS.md` — les cinq décisions d'architecture argumentées
- `docs/IA.md` — usage de l'IA pendant le projet, phase par phase
