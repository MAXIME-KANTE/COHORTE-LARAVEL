<?php

namespace Tests\Feature\Ia;

use App\Models\AppelIa;
use App\Models\Promotion;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuotaDoublonTest extends TestCase
{
    use RefreshDatabase;

    private function membre(): array
    {
        $promo = Promotion::factory()->create();
        $membre = User::factory()->create(['promotion_id' => $promo->id]);

        return [$promo, $membre];
    }

    public function test_le_quota_est_bloque_quand_il_est_epuise(): void
    {
        [, $membre] = $this->membre();

        AppelIa::factory()->count(config('cohorte.quota_ia_quotidien'))->create([
            'user_id' => $membre->id,
            'contexte' => 'moderation',
        ]);

        $this->assertTrue($membre->refresh()->quotaIaRestant() === 0);

        $this->actingAs($membre)
            ->post('/questions/verifier-doublon', ['titre' => 'Une question', 'contenu' => 'Détail'])
            ->assertRedirect();

        $this->assertGuestOrSessionHas('erreur');
    }

    private function assertGuestOrSessionHas(string $cle): void
    {
        if (session($cle) === null) {
            $this->fail("Le message flash « {$cle} » aurait dû être posé.");
        }

        $this->assertTrue(true);
    }

    public function test_chaque_appel_compte_dans_le_quota(): void
    {
        [, $membre] = $this->membre();

        AppelIa::factory()->count(3)->create(['user_id' => $membre->id, 'contexte' => 'moderation']);

        $this->assertSame(config('cohorte.quota_ia_quotidien') - 3, $membre->refresh()->quotaIaRestant());
    }

    public function test_une_question_similaire_est_proposee_avant_publication(): void
    {
        [$promo, $membre] = $this->membre();
        $existante = Publication::factory()->question()->create(['promotion_id' => $promo->id]);

        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"similaires": [' . $existante->id . ']}']]],
        ], 200)]);

        $reponse = $this->actingAs($membre)
            ->post('/questions', [
                'titre' => 'Un titre proche de la question existante',
                'contenu' => 'Un contenu suffisamment long pour la validation.',
            ]);

        // Le formulaire est réaffiché avec les questions similaires
        $reponse->assertRedirect(route('questions.create'));
        $this->assertDatabaseMissing('publications', ['user_id' => $membre->id, 'type' => 'question']);
        $this->assertDatabaseHas('appel_ias', ['user_id' => $membre->id, 'contexte' => 'doublon']);
    }

    public function test_publier_quand_meme_apres_verification(): void
    {
        [$promo, $membre] = $this->membre();

        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"verdict":"acceptable","raison":"ok"}']]],
        ], 200)]);

        $this->actingAs($membre)
            ->post('/questions', [
                'titre' => 'Un titre nouveau',
                'contenu' => 'Un contenu suffisamment long pour la validation.',
                'doublon_verifie' => '1',
            ]);

        $this->assertDatabaseHas('publications', [
            'user_id' => $membre->id,
            'type' => 'question',
            'statut' => 'publie',
        ]);
    }

    public function test_les_identifiants_inventes_par_le_modele_sont_filtres(): void
    {
        [$promo, $membre] = $this->membre();
        $etrangere = Publication::factory()->question()->create([
            'promotion_id' => Promotion::factory()->create()->id,
        ]);

        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"similaires": [' . $etrangere->id . ']}']]],
        ], 200)]);

        $reponse = $this->actingAs($membre)
            ->post('/questions/verifier-doublon', [
                'titre' => 'Un titre',
                'contenu' => 'Détail',
            ]);

        // Aucune question similaire dans SA promotion : pas de flash similaires
        $reponse->assertSessionMissing('similaires');
    }
}
