<?php

namespace Tests\Feature\Promotion;

use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdhesionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_membre_sans_promotion_peut_acceder_au_formulaire(): void
    {
        $membre = User::factory()->create(['promotion_id' => null]);

        $this->actingAs($membre)
            ->get('/rejoindre')
            ->assertOk();
    }

    public function test_un_membre_sans_promotion_est_redirige_vers_rejoindre(): void
    {
        $membre = User::factory()->create(['promotion_id' => null]);

        $this->actingAs($membre)
            ->get('/publications')
            ->assertRedirect(route('promotion.rejoindre'));
    }

    public function test_un_membre_rejoint_une_promotion_avec_un_code_valide(): void
    {
        $promotion = Promotion::factory()->create(['ouverte' => true]);
        $membre = User::factory()->create(['promotion_id' => null]);

        $this->actingAs($membre)
            ->post('/rejoindre', ['code_invitation' => $promotion->code_invitation])
            ->assertRedirect(route('publications.index'));

        $this->assertDatabaseHas('users', [
            'id' => $membre->id,
            'promotion_id' => $promotion->id,
        ]);
    }

    public function test_un_enseignant_ne_peut_pas_adherer_a_une_promotion(): void
    {
        $promotion = Promotion::factory()->create(['ouverte' => true]);
        $enseignant = User::factory()->create(['role' => 'enseignant', 'promotion_id' => null]);

        $this->actingAs($enseignant)
            ->post('/rejoindre', ['code_invitation' => $promotion->code_invitation])
            ->assertForbidden();
    }

    public function test_un_code_inconnu_est_refuse(): void
    {
        $membre = User::factory()->create(['promotion_id' => null]);

        $this->actingAs($membre)
            ->post('/rejoindre', ['code_invitation' => 'INEXISTANT'])
            ->assertSessionHasErrors('code_invitation');
    }
}
