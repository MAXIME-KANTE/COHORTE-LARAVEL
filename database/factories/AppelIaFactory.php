<?php

namespace Database\Factories;

use App\Models\AppelIa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppelIaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'contexte' => 'moderation',
            'modele' => 'test/test:free',
            'reussi' => true,
        ];
    }

    public function doublon(): static
    {
        return $this->state(fn () => ['contexte' => 'doublon']);
    }
}
