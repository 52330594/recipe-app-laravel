<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Main Dashboard & Recipe CRUD Routes
    Route::get('/dashboard', [RecipeController::class, 'index'])->name('dashboard');
    Route::resource('recipes', RecipeController::class);

    // AI Recipe Generation Endpoint
    Route::post('/recipes/ai-generate', [RecipeController::class, 'generateAi'])->name('recipes.ai');

    // Breeze Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';