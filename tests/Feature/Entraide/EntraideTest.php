<?php

namespace Tests\Feature\Entraide;

use App\Models\Publication;
use App\Models\Promotion;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EntraideTest extends TestCase
{
    use RefreshDatabase;

    public function test_retenir_une_reponse_credite_son_auteur_de_10_points(): void
    {
        $promo = Promotion::factory()->create();
        $demandeur = User::factory()->create(['promotion_id' => $promo->id]);
        $repondant = User::factory()->create(['promotion_id' => $promo->id]);

        $question = Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
            'user_id' => $demandeur->id,
        ]);
        $reponse = Reponse::factory()->create([
            'publication_id' => $question->id,
            'user_id' => $repondant->id,
        ]);

        $this->actingAs($demandeur)
            ->post("/questions/{$question->id}/reponse-retenue", ['reponse_id' => $reponse->id])
            ->assertRedirect();

        $this->assertDatabaseHas('publications', [
            'id' => $question->id,
            'reponse_retenue_id' => $reponse->id,
        ]);
        $this->assertSame(10, $repondant->fresh()->points);
    }

    public function test_une_reponse_d_une_autre_question_ne_peut_pas_etre_retenue(): void
    {
        $promo = Promotion::factory()->create();
        $demandeur = User::factory()->create(['promotion_id' => $promo->id]);

        $question1 = Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
            'user_id' => $demandeur->id,
        ]);
        $question2 = Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
            'user_id' => $demandeur->id,
        ]);
        $reponseDeQuestion2 = Reponse::factory()->create(['publication_id' => $question2->id]);

        $this->actingAs($demandeur)
            ->post("/questions/{$question1->id}/reponse-retenue", [
                'reponse_id' => $reponseDeQuestion2->id,
            ])
            ->assertForbidden();
    }

    public function test_seul_l_auteur_de_la_question_peut_designer_la_reponse_retenue(): void
    {
        $promo = Promotion::factory()->create();
        $demandeur = User::factory()->create(['promotion_id' => $promo->id]);
        $autre = User::factory()->create(['promotion_id' => $promo->id]);

        $question = Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
            'user_id' => $demandeur->id,
        ]);
        $reponse = Reponse::factory()->create(['publication_id' => $question->id]);

        $this->actingAs($autre)
            ->post("/questions/{$question->id}/reponse-retenue", ['reponse_id' => $reponse->id])
            ->assertForbidden();
    }

    public function test_un_membre_ne_peut_pas_voir_une_question_hors_promotion(): void
    {
        $promoA = Promotion::factory()->create();
        $promoB = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promoA->id]);

        $question = Publication::factory()->question()->create(['promotion_id' => $promoB->id]);

        $this->actingAs($awa)
            ->get("/questions/{$question->id}")
            ->assertForbidden();
    }

    public function test_un_membre_peut_repondre_a_une_question(): void
    {
        $promo = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promo->id]);

        $question = Publication::factory()->question()->create(['promotion_id' => $promo->id]);

        $this->actingAs($awa)
            ->post("/questions/{$question->id}/reponses", ['contenu' => 'Voici ma réponse détaillée.'])
            ->assertRedirect();

        $this->assertDatabaseHas('reponses', [
            'publication_id' => $question->id,
            'user_id' => $awa->id,
        ]);
    }
}
