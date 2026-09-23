<?php

namespace Database\Factories;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublicationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'promotion_id' => Promotion::factory(),
            'user_id' => User::factory(),
            'type' => 'post',
            'titre' => fake()->sentence(6),
            'contenu' => fake()->paragraphs(2, true),
            'statut' => 'publie',
            'created_at' => fake()->dateTimeBetween('-30 days'),
        ];
    }

    public function question(): static
    {
        return $this->state(fn () => [
            'type' => 'question',
            'titre' => rtrim(fake()->sentence(8), '.') . '?',
        ]);
    }

    public function enModeration(): static
    {
        return $this->state(fn () => ['statut' => 'en_moderation']);
    }

    public function masquee(): static
    {
        return $this->state(fn () => [
            'statut' => 'masque',
            'motif_moderation' => fake()->sentence(4),
        ]);
    }
}
