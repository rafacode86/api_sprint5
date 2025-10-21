<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\PersonalAccessTokenFactory;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear client personal para Passport (necesario para PersonalAccessTokenFactory)
        if (class_exists(\Laravel\Passport\ClientRepository::class) && Schema::hasTable('oauth_clients')) {
            $clientRepository = app(\Laravel\Passport\ClientRepository::class);
            try {
                // personalAccessClient lanza una RuntimeException si no existe
                $clientRepository->personalAccessClient();
            } catch (\RuntimeException $e) {
                $clientRepository->createPersonalAccessClient(null, 'Test Personal Access Client', 'http://localhost');
            }
        }
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function it_registers_a_user_and_returns_a_token()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Rafa Test',
            'email' => 'rafa@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email'],
                     'token'
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'rafa@test.com'
        ]);
    }

    /** @test */
    public function it_logs_in_a_registered_user_and_returns_a_token()
    {
        $user = User::factory()->create([
            'email' => 'login@test.com',
            'password' => Hash::make('12345678')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@test.com',
            'password' => '12345678',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user' => ['id', 'name', 'email'],
                     'token'
                 ]);
    }

    /** @test */
    public function it_requires_valid_credentials_to_login()
    {
        $user = User::factory()->create([
            'email' => 'wrong@test.com',
            'password' => Hash::make('12345678')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong@test.com',
            'password' => 'badpassword',
        ]);

        $response->assertStatus(422); // credenciales inválidas
    }

    /** @test */
    public function it_allows_access_to_protected_routes_with_token()
    {
        $user = User::factory()->create();

        $tokenFactory = app(PersonalAccessTokenFactory::class);
        $tokenResult = $tokenFactory->make($user->id, 'API Token', ['*']);
        $token = $tokenResult->accessToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['email' => $user->email]);
    }

    /** @test */
    public function it_denies_access_to_protected_routes_without_token()
    {
        $user = User::factory()->create();

    $response = $this->getJson("/api/users/{$user->id}");
        $response->assertStatus(401);
    }
}

