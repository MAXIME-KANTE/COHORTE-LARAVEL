<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => 'Développement Web ' . fake()->year(),
            'code_invitation' => strtoupper(fake()->unique()->bothify('??####')),
            'annee' => 2026,
            'ouverte' => true,
        ];
    }
}
