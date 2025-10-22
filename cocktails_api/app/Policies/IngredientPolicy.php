<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ingredient;

class IngredientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Ingredient $ingredient): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isAdmin();
    }

    public function update(User $user, Ingredient $ingredient): bool
    {
        return ! $user->isAdmin();
    }

    public function delete(User $user, Ingredient $ingredient): bool
    {
        return ! $user->isAdmin();
    }
}
