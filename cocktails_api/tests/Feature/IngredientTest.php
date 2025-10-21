<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\PersonalAccessTokenFactory;


class IngredientTest extends TestCase
{
    use RefreshDatabase;

    protected $token;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        // Asegurar que existe un personal access client de Passport para generar tokens
        if (class_exists(\Laravel\Passport\ClientRepository::class) && Schema::hasTable('oauth_clients')) {
            $clientRepository = app(\Laravel\Passport\ClientRepository::class);
            try {
                $clientRepository->personalAccessClient();
            } catch (\RuntimeException $e) {
                $clientRepository->createPersonalAccessClient(null, 'Test Personal Access Client', 'http://localhost');
            }
        }
        $tokenFactory = app(PersonalAccessTokenFactory::class);
        $tokenResult = $tokenFactory->make($this->user->id, 'API Token', ['*']);
        $this->token = $tokenResult->accessToken;
    }

    /** @test */
    public function it_creates_an_ingredient()
    {
        $data = [
            'name' => 'Vodka',
            'type' => 'spirit',
            'origin' => 'Russia',
            'classification' => 'alcoholic'
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/ingredients', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Vodka']);

        $this->assertDatabaseHas('ingredients', ['name' => 'Vodka']);
    }

    /** @test */
    public function it_lists_ingredients()
    {
        Ingredient::factory()->count(2)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/ingredients');

        $response->assertStatus(200)
                 ->assertJsonCount(2);
    }

    /** @test */
    public function it_updates_an_ingredient()
    {
        $ingredient = Ingredient::factory()->create(['origin' => 'Spain', 'classification' => 'juice']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/ingredients/{$ingredient->id}", [
            'origin' => 'France',
            'classification' => 'alcoholic'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['origin' => 'France', 'classification' => 'alcoholic']);

        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'origin' => 'France', 'classification' => 'alcoholic']);
    }

    /** @test */
    public function it_deletes_an_ingredient()
    {
        $ingredient = Ingredient::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/ingredients/{$ingredient->id}");

        // El controlador devuelve 200 con mensaje elimnado
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Ingrediente eliminado correctamente']);

        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }
}
