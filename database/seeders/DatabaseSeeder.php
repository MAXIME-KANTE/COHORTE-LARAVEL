<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer les promotions de test
        $promoA = Promotion::create([
            'nom' => 'Développement Web Groupe A',
            'code_invitation' => 'GRPA26',
            'annee' => 2026,
            'ouverte' => true,
        ]);

        $promoB = Promotion::create([
            'nom' => 'Développement Web Groupe B',
            'code_invitation' => 'GRPB26',
            'annee' => 2026,
            'ouverte' => true,
        ]);

        // 2. Créer les 4 comptes de démo obligatoires
        User::create([
            'name' => 'Awa Diop',
            'email' => 'awa@cohorte.test',
            'password' => Hash::make('password'),
            'role' => 'apprenant',
            'points' => 0,
            'promotion_id' => $promoA->id,
        ]);

        User::create([
            'name' => 'Moussa Ba',
            'email' => 'moussa@cohorte.test',
            'password' => Hash::make('password'),
            'role' => 'delegue',
            'points' => 10,
            'promotion_id' => $promoA->id,
        ]);

        User::create([
            'name' => 'Fatou Sow',
            'email' => 'fatou@cohorte.test',
            'password' => Hash::make('password'),
            'role' => 'apprenant',
            'points' => 0,
            'promotion_id' => $promoB->id,
        ]);

        User::create([
            'name' => 'Formateur',
            'email' => 'formateur@cohorte.test',
            'password' => Hash::make('password'),
            'role' => 'enseignant',
            'points' => 100,
            'promotion_id' => null,
        ]);
    }
}
