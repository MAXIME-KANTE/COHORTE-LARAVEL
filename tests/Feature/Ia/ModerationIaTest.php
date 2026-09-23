<?php

namespace Tests\Feature\Ia;

use App\Models\Publication;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ModerationIaTest extends TestCase
{
    use RefreshDatabase;

    private function membre(): User
    {
        $promotion = Promotion::factory()->create();

        return User::factory()->create(['promotion_id' => $promotion->id]);
    }

    private function publier(User $auteur, string $contenu): \Illuminate\Testing\TestResponse
    {
        return $this->actingAs($auteur)->post('/publications', [
            'titre' => 'Un titre',
            'contenu' => $contenu,
        ]);
    }

    public function test_verdict_acceptable_publie_la_publication(): void
    {
        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"verdict":"acceptable","raison":"contenu normal"}']]],
        ], 200)]);

        $this->publier($this->membre(), 'Un contenu tout à fait normal pour un exercice Laravel.');

        $this->assertDatabaseHas('publications', ['statut' => 'publie', 'motif_moderation' => 'acceptable']);
        $this->assertDatabaseHas('appel_ias', ['contexte' => 'moderation', 'reussi' => true]);
    }

    public function test_verdict_inacceptable_refuse_la_publication(): void
    {
        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"verdict":"inacceptable","raison":"insulte"}']]],
        ], 200)]);

        $auteur = $this->membre();
        $this->publier($auteur, 'Un contenu grossier et injurieux envers un camarade.');

        $this->assertDatabaseHas('publications', ['statut' => 'refuse', 'motif_moderation' => 'inacceptable']);
    }

    public function test_service_en_panne_envoie_en_file_de_moderation(): void
    {
        config(['cohorte.moderation_fail_open' => false]);
        Http::fake(['openrouter.ai/*' => Http::response(null, 503)]);

        $this->publier($this->membre(), 'Un contenu quelconque pendant la panne du service.');

        $this->assertDatabaseHas('publications', ['statut' => 'en_moderation', 'motif_moderation' => 'indisponible']);
        $this->assertDatabaseHas('appel_ias', ['contexte' => 'moderation', 'reussi' => false]);
    }

    public function test_reponse_illisible_du_modele_est_traitee_comme_indisponible(): void
    {
        config(['cohorte.moderation_fail_open' => false]);
        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Bonjour ! Voici mon analyse de la publication...']]],
        ], 200)]);

        $this->publier($this->membre(), 'Un contenu pendant que le modèle bavarde au lieu de répondre en JSON.');

        $this->assertDatabaseHas('publications', ['statut' => 'en_moderation', 'motif_moderation' => 'indisponible']);
    }

    public function test_fail_open_publie_malgre_la_panne(): void
    {
        config(['cohorte.moderation_fail_open' => true]);
        Http::fake(['openrouter.ai/*' => Http::response(null, 503)]);

        $this->publier($this->membre(), 'Un contenu quelconque en mode fail-open.');

        $this->assertDatabaseHas('publications', ['statut' => 'publie']);
    }
}
