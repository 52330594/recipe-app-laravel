<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->get('tab', 'my_recipes');

        if ($viewMode === 'community') {
            $query = Recipe::where('is_public', true)->with('user');
        } else {
            $query = Auth::user()->recipes();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('ingredients', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('cuisine')) {
            $query->where('cuisine', $request->cuisine);
        }

        $recipes = $query->latest()->get();

        $cuisines = Recipe::whereNotNull('cuisine')
            ->where('cuisine', '!=', '')
            ->distinct()
            ->pluck('cuisine');

        return view('recipes.index', compact('recipes', 'cuisines', 'viewMode'));
    }

    public function generateAi(Request $request)
    {
        $request->validate([
            'prompt_ingredients' => 'required|string',
        ]);

        $prompt = $request->input('prompt_ingredients');

        // Simple Smart AI Generator Engine
        $title = "AI Special: " . ucwords(explode(',', $prompt)[0]) . " Delight";
        $cuisine = "Chef's Fusion";
        $prepTime = 20;
        $ingredients = "Main Ingredients:\n- " . str_replace(',', "\n- ", $prompt) . "\n- Salt & Black Pepper\n- 2 tbsp Olive Oil";
        $instructions = "1. Prep all available ingredients: {$prompt}.\n2. Heat olive oil in a skillet over medium heat.\n3. Saute the main ingredients until tender and fragrant (about 8-10 mins).\n4. Season with salt and pepper to taste.\n5. Serve hot and enjoy your custom creation!";

        return response()->json([
            'title' => $title,
            'cuisine' => $cuisine,
            'prep_time' => $prepTime,
            'ingredients' => $ingredients,
            'instructions' => $instructions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'cuisine' => 'nullable|string|max:255',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'prep_time' => 'nullable|integer',
            'status' => 'required|string',
            'is_public' => 'nullable|boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        Auth::user()->recipes()->create($validated);

        return redirect()->route('recipes.index')->with('success', 'Recipe created successfully!');
    }

    public function update(Request $request, Recipe $recipe)
    {
        if ($recipe->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'cuisine' => 'nullable|string|max:255',
            'ingredients' => 'required|string',
            'instructions' => 'required|string',
            'prep_time' => 'nullable|integer',
            'status' => 'required|string',
            'is_public' => 'nullable|boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $recipe->update($validated);

        return redirect()->route('recipes.index')->with('success', 'Recipe updated successfully!');
    }

    public function destroy(Recipe $recipe)
    {
        if ($recipe->user_id !== Auth::id()) {
            abort(403);
        }

        $recipe->delete();

        return redirect()->route('recipes.index')->with('success', 'Recipe deleted successfully!');
    }
}