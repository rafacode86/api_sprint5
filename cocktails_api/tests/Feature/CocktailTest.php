<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Cocktail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\PersonalAccessTokenFactory;

class CocktailTest extends TestCase
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
    public function it_creates_a_cocktail()
    {
        $data = [
            'name' => 'Margarita',
            'type' => 'alcoholic',
            'description' => 'Cocktail clásico de lima',
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->postJson('/api/cocktails', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Margarita']);

        $this->assertDatabaseHas('cocktails', ['name' => 'Margarita']);
    }

    /** @test */
    public function it_lists_all_cocktails()
    {
        Cocktail::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->getJson('/api/cocktails');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_updates_a_cocktail()
    {
        $cocktail = Cocktail::factory()->create(['type' => 'alcoholic']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->putJson("/api/cocktails/{$cocktail->id}", [
            'type' => 'non-alcoholic',
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['type' => 'non-alcoholic']);

        $this->assertDatabaseHas('cocktails', ['id' => $cocktail->id, 'type' => 'non-alcoholic']);
    }

    /** @test */
    public function it_deletes_a_cocktail()
    {
        $cocktail = Cocktail::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$this->token}",
        ])->deleteJson("/api/cocktails/{$cocktail->id}");

        // El controlador devuelve 200 con mensaje al eliminar
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Cóctel eliminado correctamente']);

        $this->assertDatabaseMissing('cocktails', ['id' => $cocktail->id]);
    }
}
