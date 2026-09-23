<?php

namespace App\Services;

use App\Models\AppelIa;
use App\Models\Publication;
use App\Models\User;

class ServiceDetectionDoublon
{
    public function __construct(private OpenRouterClient $client)
    {
    }

    /**
     * Renvoie les questions existantes jugées similaires.
     */
    public function chercherSimilaires(string $titre, User $auteur): array
    {
        $existantes = Publication::query()
            ->questions()
            ->visibles()
            ->deLaPromotion($auteur->promotion_id)
            ->latest()
            ->limit(40)
            ->pluck('titre', 'id');

        if ($existantes->isEmpty()) {
            return [];
        }

        $catalogue = $existantes
            ->map(fn ($t, $id) => "{$id}. {$t}")
            ->implode("\n");

        $debut = microtime(true);

        $texte = $this->client->discuter([
            ['role' => 'system', 'content' => $this->consigne()],
            ['role' => 'user', 'content' => "Questions existantes :\n{$catalogue}\n\nNouvelle question :\n{$titre}"],
        ]);

        AppelIa::create([
            'user_id' => $auteur->id,
            'contexte' => 'doublon',
            'modele' => config('services.openrouter.model'),
            'reussi' => $texte !== null,
            'duree_ms' => (int) ((microtime(true) - $debut) * 1000),
        ]);

        $ids = $this->extraireIdentifiants($texte);

        // Le modèle peut inventer un identifiant : on re-filtre sur la promotion
        return Publication::whereIn('id', $ids)
            ->deLaPromotion($auteur->promotion_id)
            ->get()
            ->all();
    }

    private function consigne(): string
    {
        return <<<'TXT'
        On te donne une liste de questions déjà posées, numérotées, et une nouvelle question.
        Identifie celles qui traitent du même problème que la nouvelle question.
        Sois strict : une simple proximité de vocabulaire ne suffit pas.

        Réponds UNIQUEMENT par un objet JSON de la forme {"similaires": [12, 45]}.
        Si aucune question n'est similaire, réponds {"similaires": []}.
        TXT;
    }

    private function extraireIdentifiants(?string $texte): array
    {
        if ($texte === null) {
            return [];
        }

        if (preg_match('/\{.*\}/s', $texte, $trouve)) {
            $texte = $trouve[0];
        }

        $donnees = json_decode($texte, true);

        if (! is_array($donnees) || ! isset($donnees['similaires']) || ! is_array($donnees['similaires'])) {
            return [];
        }

        return array_filter(array_map('intval', $donnees['similaires']));
    }
}
