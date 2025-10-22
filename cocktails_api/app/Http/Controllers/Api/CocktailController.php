<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cocktail;
use App\Models\Ingredient;

class CocktailController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(\App\Models\Cocktail::class, 'cocktail');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cocktails = Cocktail::with('ingredients')->get();
        return response()->json($cocktails, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:alcoholic,non-alcoholic',
            'ingredients' => 'array',
            'ingredients.*.id' => 'exists:ingredients,id',
            'ingredients.*.amount' => 'numeric|min:1'
        ]);

        $cocktail = Cocktail::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type']
        ]);

        // Si hay ingredientes, los asociamos
        if (!empty($validated['ingredients'])) {
            foreach ($validated['ingredients'] as $ing) {
                $cocktail->ingredients()->attach($ing['id'], ['amount' => $ing['amount']]);
            }
        }

        return response()->json([
            'message' => 'Cóctel creado correctamente',
            'cocktail' => $cocktail->load('ingredients')
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cocktail $cocktail)
    {
        return response()->json($cocktail->load('ingredients'), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cocktail $cocktail)
    {

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'description' => 'nullable|string',
            'type' => 'sometimes|required|in:alcoholic,non-alcoholic',
            'ingredients' => 'array',
            'ingredients.*.id' => 'exists:ingredients,id',
            'ingredients.*.amount' => 'numeric|min:1'
        ]);

    $cocktail->update($validated);

        if (!empty($validated['ingredients'])) {
            $cocktail->ingredients()->detach();
            foreach ($validated['ingredients'] as $ing) {
                $cocktail->ingredients()->attach($ing['id'], ['amount' => $ing['amount']]);
            }
        }

        return response()->json([
            'message' => 'Cóctel actualizado correctamente',
            'cocktail' => $cocktail->load('ingredients')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cocktail $cocktail)
    {
        $cocktail->ingredients()->detach();
        $cocktail->delete();

        return response()->json(['message' => 'Cóctel eliminado correctamente'], 200);
    }
}
