<?php

namespace Tests\Feature\Feed;

use App\Models\Publication;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CloisonnementTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_apprenant_ne_peut_pas_voir_une_publication_hors_promotion(): void
    {
        $promoA = Promotion::factory()->create();
        $promoB = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promoA->id]);
        $publication = Publication::factory()->create(['promotion_id' => $promoB->id]);

        $this->actingAs($awa)
            ->get("/publications/{$publication->id}")
            ->assertForbidden();
    }

    public function test_le_fil_ne_montre_que_les_publications_de_sa_promotion(): void
    {
        $promoA = Promotion::factory()->create();
        $promoB = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promoA->id]);

        $publicationA = Publication::factory()->create(['promotion_id' => $promoA->id]);
        Publication::factory()->create(['promotion_id' => $promoB->id]);

        $reponse = $this->actingAs($awa)->get('/publications');

        $reponse->assertOk()
            ->assertSee($publicationA->titre)
            ->assertDontSeeText(Publication::where('promotion_id', $promoB->id)->first()->titre);
    }

    public function test_l_auteur_voit_sa_propre_publication_masquee(): void
    {
        $promo = Promotion::factory()->create();
        $auteur = User::factory()->create(['promotion_id' => $promo->id]);
        $publication = Publication::factory()->masquee()->create([
            'promotion_id' => $promo->id,
            'user_id' => $auteur->id,
        ]);

        $this->actingAs($auteur)
            ->get("/publications/{$publication->id}")
            ->assertOk();
    }

    public function test_un_membre_ne_peut_pas_supprimer_la_publication_d_un_autre(): void
    {
        $promo = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promo->id]);
        $publication = Publication::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($awa)
            ->delete("/publications/{$publication->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('publications', ['id' => $publication->id]);
    }

    public function test_un_apprenant_peut_publier_dans_son_fil(): void
    {
        // La modération IA est simulée : verdict acceptable, publication directe
        Http::fake(['openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => '{"verdict":"acceptable","raison":"contenu normal"}']]],
        ], 200)]);

        $promo = Promotion::factory()->create();
        $awa = User::factory()->create(['promotion_id' => $promo->id]);

        $this->actingAs($awa)
            ->post('/publications', [
                'titre' => 'Bonjour',
                'contenu' => 'Ma première publication dans le fil.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('publications', [
            'user_id' => $awa->id,
            'promotion_id' => $promo->id,
            'type' => 'post',
            'statut' => 'publie',
        ]);
    }
}
