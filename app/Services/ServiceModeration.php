<?php

namespace App\Services;

use App\Enums\VerdictModeration;
use App\Models\AppelIa;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ServiceModeration
{
    public function __construct(private OpenRouterClient $client)
    {
    }

    public function evaluer(string $contenu, User $auteur): VerdictModeration
    {
        $debut = microtime(true);

        $texte = $this->client->discuter([
            [
                'role' => 'system',
                'content' => $this->consigne(),
            ],
            [
                'role' => 'user',
                'content' => "Publication à évaluer :\n\n" . $contenu,
            ],
        ]);

        $verdict = $this->interpreter($texte);

        AppelIa::create([
            'user_id' => $auteur->id,
            'contexte' => 'moderation',
            'modele' => config('services.openrouter.model'),
            'reussi' => $verdict !== VerdictModeration::Indisponible,
            'duree_ms' => (int) ((microtime(true) - $debut) * 1000),
        ]);

        return $verdict;
    }

    private function consigne(): string
    {
        return <<<'TXT'
        Tu es le modérateur d'un réseau social interne à une école de développement web.
        Tu évalues si une publication est acceptable dans un cadre scolaire.

        Classe la publication dans l'une de ces trois catégories :
        - "acceptable" : contenu normal, même maladroit ou hors sujet
        - "douteux" : moquerie ciblée, propos limites, publicité, contenu ambigu
        - "inacceptable" : insulte, harcèlement, propos haineux, contenu sexuel

        Réponds UNIQUEMENT par un objet JSON valide, sans texte avant ni après,
        de la forme exacte : {"verdict": "acceptable", "raison": "une phrase courte"}
        TXT;
    }

    private function interpreter(?string $texte): VerdictModeration
    {
        if ($texte === null) {
            return VerdictModeration::Indisponible;
        }

        // Le modèle encadre parfois sa réponse par ```json ... ```
        if (preg_match('/\{.*\}/s', $texte, $trouve)) {
            $texte = $trouve[0];
        }

        $donnees = json_decode($texte, true);

        if (! is_array($donnees) || ! isset($donnees['verdict'])) {
            Log::warning('Reponse de moderation illisible', ['brut' => $texte]);

            return VerdictModeration::Indisponible;
        }

        return VerdictModeration::tryFrom($donnees['verdict']) ?? VerdictModeration::Indisponible;
    }
}
