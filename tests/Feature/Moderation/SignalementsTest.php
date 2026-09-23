<?php

namespace Tests\Feature\Moderation;

use App\Models\Publication;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SignalementsTest extends TestCase
{
    use RefreshDatabase;

    private function scenario(): array
    {
        $promo = Promotion::factory()->create();
        $auteur = User::factory()->create(['promotion_id' => $promo->id]);
        $publication = Publication::factory()->create([
            'promotion_id' => $promo->id,
            'user_id' => $auteur->id,
        ]);

        return [$promo, $auteur, $publication];
    }

    private function signaler(User $membre, Publication $publication): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($membre)
            ->post("/publications/{$publication->id}/signalements", ['motif' => 'insulte']);
    }

    public function test_un_membre_peut_signaler_une_publication(): void
    {
        [$promo, $auteur, $publication] = $this->scenario();
        $membre = User::factory()->create(['promotion_id' => $promo->id]);

        $this->signaler($membre, $publication);

        $this->assertDatabaseHas('signalements', [
            'publication_id' => $publication->id,
            'user_id' => $membre->id,
        ]);
    }

    public function test_on_ne_signale_pas_sa_propre_publication(): void
    {
        [, $auteur, $publication] = $this->scenario();

        $this->signaler($auteur, $publication)->assertForbidden();

        $this->assertDatabaseCount('signalements', 0);
    }

    public function test_on_ne_signale_pas_deux_fois(): void
    {
        [$promo, , $publication] = $this->scenario();
        $membre = User::factory()->create(['promotion_id' => $promo->id]);

        $this->signaler($membre, $publication);
        $this->signaler($membre, $publication);

        $this->assertDatabaseCount('signalements', 1);
        $this->assertDatabaseHas('publications', ['id' => $publication->id, 'statut' => 'publie']);
    }

    public function test_au_seuil_la_publication_est_masquee(): void
    {
        [$promo, , $publication] = $this->scenario();

        foreach (range(1, config('cohorte.seuil_signalement')) as $i) {
            $membre = User::factory()->create(['promotion_id' => $promo->id]);
            $this->signaler($membre, $publication);
        }

        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'statut' => 'masque',
        ]);
    }

    public function test_on_ne_signale_pas_une_publication_d_une_autre_promotion(): void
    {
        [, , $publication] = $this->scenario();
        $etranger = User::factory()->create(['promotion_id' => Promotion::factory()->create()->id]);

        $this->signaler($etranger, $publication)->assertForbidden();
    }

    public function test_le_delegue_ne_modere_pas_une_autre_promotion(): void
    {
        [, , $publication] = $this->scenario();

        $delegueEtranger = User::factory()->create([
            'role' => 'delegue',
            'promotion_id' => Promotion::factory()->create()->id,
        ]);

        $this->actingAs($delegueEtranger)
            ->put("/moderation/{$publication->id}", ['decision' => 'valider'])
            ->assertForbidden();
    }

    public function test_le_delegue_peut_valider_une_publication_en_attente(): void
    {
        [$promo, , $publication] = $this->scenario();
        $publication->update(['statut' => 'en_moderation']);

        $delegue = User::factory()->create(['role' => 'delegue', 'promotion_id' => $promo->id]);

        $this->actingAs($delegue)
            ->put("/moderation/{$publication->id}", ['decision' => 'valider']);

        $this->assertDatabaseHas('publications', [
            'id' => $publication->id,
            'statut' => 'publie',
        ]);
    }

    public function test_un_apprenant_n_a_pas_acces_a_la_file_de_moderation(): void
    {
        [$promo, , ] = $this->scenario();
        $apprenant = User::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($apprenant)->get('/moderation')->assertForbidden();
    }
}
