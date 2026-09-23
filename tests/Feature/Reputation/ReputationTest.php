<?php

namespace Tests\Feature\Reputation;

use App\Models\Promotion;
use App\Models\Publication;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReputationTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_commande_recalcule_le_score_de_contribution(): void
    {
        $promo = Promotion::factory()->create();
        $membre = User::factory()->create(['promotion_id' => $promo->id, 'points' => 999]);

        // 1 question posée, 2 réponses écrites dont 1 retenue par un autre membre
        $question = Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
        ]);
        Publication::factory()->question()->create([
            'promotion_id' => $promo->id,
            'user_id' => $membre->id,
        ]);
        $retenue = Reponse::factory()->create([
            'publication_id' => $question->id,
            'user_id' => $membre->id,
        ]);
        Reponse::factory()->create(['publication_id' => $question->id, 'user_id' => $membre->id]);
        $question->update(['reponse_retenue_id' => $retenue->id]);

        Artisan::call('cohorte:recalculer-reputation');

        // 1 réponse retenue ×10 + 2 réponses ×3 + 1 question ×1 = 17
        $this->assertSame(17, $membre->refresh()->points);
    }

    public function test_epinglage_refuse_sous_le_seuil(): void
    {
        $promo = Promotion::factory()->create();
        $membre = User::factory()->create([
            'promotion_id' => $promo->id,
            'points' => config('cohorte.seuil_epinglage') - 1,
            'role' => 'apprenant',
        ]);
        $publication = Publication::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($membre)
            ->post("/publications/{$publication->id}/epingle")
            ->assertForbidden();

        $this->assertDatabaseMissing('publications', [
            'id' => $publication->id,
            'epingle_le' => now(),
        ]);
    }

    public function test_epinglage_autorise_au_dela_du_seuil(): void
    {
        $promo = Promotion::factory()->create();
        $membre = User::factory()->create([
            'promotion_id' => $promo->id,
            'points' => config('cohorte.seuil_epinglage'),
        ]);
        $publication = Publication::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($membre)
            ->post("/publications/{$publication->id}/epingle");

        $this->assertNotNull($publication->refresh()->epingle_le);
    }

    public function test_le_delegue_peut_epingler_sans_seuil(): void
    {
        $promo = Promotion::factory()->create();
        $delegue = User::factory()->create([
            'promotion_id' => $promo->id,
            'role' => 'delegue',
            'points' => 0,
        ]);
        $publication = Publication::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($delegue)
            ->post("/publications/{$publication->id}/epingle");

        $this->assertNotNull($publication->refresh()->epingle_le);
    }
}
