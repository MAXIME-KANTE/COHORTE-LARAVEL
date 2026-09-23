<?php

namespace App\Console\Commands;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Console\Command;

class RecalculerReputation extends Command
{
    protected $signature = 'cohorte:recalculer-reputation';

    protected $description = 'Recalcule le score de contribution de tous les membres';

    public function handle(): int
    {
        User::query()->chunkById(100, function ($membres) {
            foreach ($membres as $membre) {
                // Combien de questions ont retenu une réponse de ce membre ?
                $reponsesRetenues = Publication::query()
                    ->whereIn('reponse_retenue_id', $membre->reponses()->select('id'))
                    ->count();

                $score = $reponsesRetenues * 10
                    + $membre->reponses()->count() * 3
                    + $membre->publications()->questions()->count()
                    - $membre->publications()->where('statut', 'refuse')->count() * 5;

                $membre->update(['points' => max(0, $score)]);
            }
        });

        $this->info('Réputation recalculée.');

        return self::SUCCESS;
    }
}
