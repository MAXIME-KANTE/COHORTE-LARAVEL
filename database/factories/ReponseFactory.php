<?php

namespace Database\Factories;

use App\Models\Publication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'publication_id' => Publication::factory(),
            'user_id' => User::factory(),
            'contenu' => fake()->paragraphs(2, true),
        ];
    }
}
