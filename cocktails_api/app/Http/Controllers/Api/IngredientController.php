<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ingredient::class, 'ingredient');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Ingredient::all(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'type' => 'nullable|string|max:50',
            'origin' => 'required|string|max:100',
            'classification' => 'required|in:alcoholic,soda,juice,garnish'
        ]);

        $ingredient = Ingredient::create($validated);

        return response()->json([
            'message' => 'Ingrediente creado correctamente',
            'ingredient' => $ingredient
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ingredient $ingredient)
    {
        return response()->json($ingredient, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ingredient $ingredient)
    {

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'type' => 'nullable|string|max:50',
            'origin' => 'sometimes|required|string|max:100',
            'classification' => 'sometimes|required|in:alcoholic,soda,juice,garnish'
        ]);

    $ingredient->update($validated);

        return response()->json([
            'message' => 'Ingrediente actualizado correctamente',
            'ingredient' => $ingredient
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return response()->json(['message' => 'Ingrediente eliminado correctamente'], 200);
    }
}
