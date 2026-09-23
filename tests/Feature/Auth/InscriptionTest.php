<?php

namespace Tests\Feature\Auth;

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscription_avec_code_valide_rattache_a_la_promotion(): void
    {
        $promotion = Promotion::factory()->create(['ouverte' => true]);

        $reponse = $this->post('/register', [
            'name' => 'Test Apprenant',
            'email' => 'test@cohorte.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code_invitation' => $promotion->code_invitation,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@cohorte.test',
            'promotion_id' => $promotion->id,
            'role' => 'apprenant',
        ]);
    }

    public function test_inscription_avec_code_inconnu_est_refusee(): void
    {
        $reponse = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test2@cohorte.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code_invitation' => 'INEXISTANT',
        ]);

        $reponse->assertSessionHasErrors('code_invitation');
        $this->assertDatabaseMissing('users', ['email' => 'test2@cohorte.test']);
    }

    public function test_inscription_sur_promotion_fermee_est_refusee(): void
    {
        $promotion = Promotion::factory()->create(['ouverte' => false]);

        $reponse = $this->post('/register', [
            'name' => 'Test',
            'email' => 'test3@cohorte.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'code_invitation' => $promotion->code_invitation,
        ]);

        $reponse->assertSessionHasErrors('code_invitation');
    }
}
