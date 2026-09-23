<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterClient
{
    /**
     * Envoie une conversation au modèle et renvoie le texte de la réponse,
     * ou null si le service est indisponible.
     */
    public function discuter(array $messages): ?string
    {
        $debut = microtime(true);

        try {
            $reponse = Http::withToken(config('services.openrouter.key'))
                ->withHeaders([
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => 'Cohorte',
                ])
                ->timeout(config('services.openrouter.timeout'))
                ->retry(2, 400, throw: false)
                ->post(config('services.openrouter.url'), [
                    'model' => config('services.openrouter.model'),
                    'messages' => $messages,
                    'temperature' => 0,
                    'max_tokens' => 300,
                ]);
        } catch (\Throwable $e) {
            Log::warning('OpenRouter injoignable', ['message' => $e->getMessage()]);

            return null;
        }

        if ($reponse->failed()) {
            Log::warning('OpenRouter a repondu en erreur', [
                'statut' => $reponse->status(),
                'corps' => $reponse->body(),
            ]);

            return null;
        }

        Log::info('Appel OpenRouter', [
            'duree_ms' => (int) ((microtime(true) - $debut) * 1000),
        ]);

        return data_get($reponse->json(), 'choices.0.message.content');
    }
}
