<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Cocktail;
use App\Models\Ingredient;
use App\Policies\CocktailPolicy;
use App\Policies\IngredientPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cocktail::class => CocktailPolicy::class,
        Ingredient::class => IngredientPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();

        //Passport::ignoreMigrations();
    }
}
