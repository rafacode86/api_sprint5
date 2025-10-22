<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ingredient;
use App\Models\Cocktail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuario de desarrollo con contraseña conocida
        User::factory()->create([
            'name' => 'Dev User',
            'email' => 'dev@example.com',
            'password' => 'secret123'
        ]);

        // Ingredientes
        Ingredient::factory()->count(10)->create();

        // Cócteles y relaciones con ingredientes
        Cocktail::factory()->count(5)->create()->each(function ($cocktail) {
            $ingredientIds = Ingredient::inRandomOrder()->take(rand(2,4))->pluck('id')->toArray();
            foreach ($ingredientIds as $id) {
                $cocktail->ingredients()->attach($id, ['amount' => rand(10,120)]);
            }
        });
    }
}
