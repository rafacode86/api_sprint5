<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CocktailController;
use App\Http\Controllers\Api\IngredientController;

// públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// protegidas (requieren token)
Route::middleware('auth:api')->group(function () {
    Route::get('/users/{id}', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);

// CRUD Cocktails
Route::apiResource('cocktails', CocktailController::class);

// CRUD Ingredients
Route::apiResource('ingredients', IngredientController::class);
});
