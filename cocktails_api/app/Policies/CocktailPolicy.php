<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Cocktail;

class CocktailPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Cocktail $cocktail): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return ! $user->isAdmin();
    }

    public function update(User $user, Cocktail $cocktail): bool
    {
        return ! $user->isAdmin();
    }

    public function delete(User $user, Cocktail $cocktail): bool
    {
        return ! $user->isAdmin();
    }
}
