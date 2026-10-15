<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recipe Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800 font-sans min-h-screen" x-data="recipeApp()">
    <div class="max-w-4xl mx-auto p-6">
        <!-- Header & Auth Navigation -->
        <header class="flex justify-between items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-3xl font-bold text-emerald-600">Recipe Manager</h1>
                <p class="text-xs text-gray-500">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
            <div class="flex items-center space-x-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm bg-gray-200 text-gray-700 hover:bg-gray-300 px-3 py-1.5 rounded transition font-medium">
                        Log Out
                    </button>
                </form>
            </div>
        </header>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-800 p-4 rounded mb-6 border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation (My Recipes vs Community) -->
        <div class="flex justify-between items-center mb-6 border-b pb-2">
            <div class="flex space-x-2">
                <a href="{{ route('recipes.index', ['tab' => 'my_recipes']) }}" 
                   class="px-4 py-2 rounded-t font-semibold text-sm {{ $viewMode !== 'community' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    My Recipes
                </a>
                <a href="{{ route('recipes.index', ['tab' => 'community']) }}" 
                   class="px-4 py-2 rounded-t font-semibold text-sm {{ $viewMode === 'community' ? 'bg-emerald-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                    Community Gallery (Shared)
                </a>
            </div>

            @if($viewMode !== 'community')
                <button @click="showAiModal = true" class="bg-purple-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-purple-700 transition flex items-center space-x-1">
                    <span>✨ AI Recipe Assistant</span>
                </button>
            @endif
        </div>

        <!-- Add Recipe Form (Only on My Recipes tab) -->
        @if($viewMode !== 'community')
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-700">Add New Recipe</h2>
            <form action="{{ route('recipes.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Title</label>
                        <input type="text" name="title" x-model="form.title" required class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cuisine</label>
                        <input type="text" name="cuisine" x-model="form.cuisine" placeholder="e.g. Italian, Mexican" class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Prep Time (mins)</label>
                        <input type="number" name="prep_time" x-model="form.prep_time" class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Status</label>
                        <select name="status" class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="To Try">To Try</option>
                            <option value="Favorite">Favorite</option>
                            <option value="Made Before">Made Before</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Ingredients</label>
                    <textarea name="ingredients" x-model="form.ingredients" rows="3" required class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Instructions</label>
                    <textarea name="instructions" x-model="form.instructions" rows="3" required class="w-full border rounded p-2 focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>

                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="is_public" id="is_public" value="1" class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                    <label for="is_public" class="text-sm font-medium text-gray-700">Share with Community (Make Public)</label>
                </div>

                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded hover:bg-emerald-700 transition font-medium">
                    Save Recipe
                </button>
            </form>
        </div>
        @endif

        <!-- Filter & Search Controls -->
        <div class="bg-white p-4 rounded-lg shadow-md mb-8">
            <form action="{{ route('recipes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="tab" value="{{ $viewMode }}">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title or ingredients..." class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>
                <div>
                    <select name="status" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">All Statuses</option>
                        <option value="To Try" {{ request('status') == 'To Try' ? 'selected' : '' }}>To Try</option>
                        <option value="Favorite" {{ request('status') == 'Favorite' ? 'selected' : '' }}>Favorite</option>
                        <option value="Made Before" {{ request('status') == 'Made Before' ? 'selected' : '' }}>Made Before</option>
                    </select>
                </div>
                <div>
                    <select name="cuisine" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="">All Cuisines</option>
                        @foreach($cuisines as $c)
                            <option value="{{ $c }}" {{ request('cuisine') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded text-sm hover:bg-emerald-700 transition font-medium flex-1">Filter</button>
                    @if(request()->hasAny(['search', 'status', 'cuisine']))
                        <a href="{{ route('recipes.index', ['tab' => $viewMode]) }}" class="bg-gray-200 text-gray-700 px-3 py-2 rounded text-sm hover:bg-gray-300 transition flex items-center justify-center">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Recipe Cards List -->
        <h2 class="text-2xl font-bold mb-4 text-gray-700">
            {{ $viewMode === 'community' ? 'Community Shared Recipes' : 'My Recipes' }}
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($recipes as $recipe)
                <div x-data="{ openEdit: false }" class="bg-white p-6 rounded-lg shadow-md border-l-4 border-emerald-500 relative flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-800">{{ $recipe->title }}</h3>
                            <div class="flex items-center space-x-1">
                                @if($recipe->is_public)
                                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full font-semibold">Public</span>
                                @endif
                                <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded-full font-semibold">
                                    {{ $recipe->status }}
                                </span>
                            </div>
                        </div>

                        <p class="text-xs text-gray-400 mb-2">
                            By: <span class="font-medium text-gray-600">{{ $recipe->user->name ?? 'Unknown' }}</span>
                        </p>

                        <p class="text-sm text-gray-500 mb-3">
                            Cuisine: <span class="font-medium text-gray-700">{{ $recipe->cuisine ?? 'N/A' }}</span> | 
                            Prep: <span class="font-medium text-gray-700">{{ $recipe->prep_time ? $recipe->prep_time . ' mins' : 'N/A' }}</span>
                        </p>

                        <div class="mb-3">
                            <h4 class="text-sm font-semibold text-gray-700">Ingredients:</h4>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $recipe->ingredients }}</p>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700">Instructions:</h4>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $recipe->instructions }}</p>
                        </div>
                    </div>

                    @if($recipe->user_id === Auth::id())
                    <div class="flex items-center space-x-2 pt-4 border-t">
                        <button @click="openEdit = true" class="bg-amber-500 text-white text-xs px-3 py-1.5 rounded hover:bg-amber-600 transition font-medium">
                            Edit
                        </button>
                        <form action="{{ route('recipes.destroy', $recipe) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this recipe?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-rose-500 text-white text-xs px-3 py-1.5 rounded hover:bg-rose-600 transition font-medium">
                                Delete
                            </button>
                        </form>
                    </div>

                    <!-- Modal Window for Editing Recipe -->
                    <div x-show="openEdit" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                        <div class="bg-white rounded-lg p-6 max-w-lg w-full space-y-4 shadow-xl" @click.away="openEdit = false">
                            <div class="flex justify-between items-center border-b pb-2">
                                <h3 class="text-lg font-bold text-gray-700">Edit Recipe</h3>
                                <button @click="openEdit = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                            </div>
                            <form action="{{ route('recipes.update', $recipe) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PUT')
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                    <input type="text" name="title" value="{{ $recipe->title }}" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Cuisine</label>
                                        <input type="text" name="cuisine" value="{{ $recipe->cuisine }}" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Prep Time (mins)</label>
                                        <input type="number" name="prep_time" value="{{ $recipe->prep_time }}" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                    <select name="status" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">
                                        <option value="To Try" {{ $recipe->status == 'To Try' ? 'selected' : '' }}>To Try</option>
                                        <option value="Favorite" {{ $recipe->status == 'Favorite' ? 'selected' : '' }}>Favorite</option>
                                        <option value="Made Before" {{ $recipe->status == 'Made Before' ? 'selected' : '' }}>Made Before</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Ingredients</label>
                                    <textarea name="ingredients" rows="2" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">{{ $recipe->ingredients }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Instructions</label>
                                    <textarea name="instructions" rows="2" required class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-emerald-500 outline-none">{{ $recipe->instructions }}</textarea>
                                </div>
                                <div class="flex items-center space-x-2 pt-1">
                                    <input type="checkbox" name="is_public" id="edit_is_public_{{ $recipe->id }}" value="1" {{ $recipe->is_public ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                    <label for="edit_is_public_{{ $recipe->id }}" class="text-xs font-medium text-gray-700">Share with Community (Public)</label>
                                </div>
                                <div class="flex justify-end space-x-2 pt-2 border-t">
                                    <button type="button" @click="openEdit = false" class="px-4 py-1.5 border rounded text-xs text-gray-600 hover:bg-gray-100 transition">Cancel</button>
                                    <button type="submit" class="bg-emerald-600 text-white px-4 py-1.5 rounded text-xs font-medium hover:bg-emerald-700 transition">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            @empty
                <p class="text-gray-500 col-span-2 text-center py-6">No recipes found in this view.</p>
            @endforelse
        </div>

        <!-- AI Assistant Modal -->
        <div x-show="showAiModal" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full space-y-4 shadow-xl" @click.away="showAiModal = false">
                <div class="flex justify-between items-center border-b pb-2">
                    <h3 class="text-lg font-bold text-purple-700">✨ AI Recipe Generator</h3>
                    <button @click="showAiModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>
                <p class="text-xs text-gray-500">Enter ingredients you have in your kitchen, and our AI will generate a custom recipe directly into your form!</p>
                
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Your Available Ingredients</label>
                    <input type="text" x-model="aiPrompt" placeholder="e.g. Chicken, Garlic, Tomatoes, Basil" class="w-full border rounded p-2 text-sm focus:ring-2 focus:ring-purple-500 outline-none">
                </div>

                <div class="flex justify-end space-x-2 pt-2 border-t">
                    <button type="button" @click="showAiModal = false" class="px-4 py-1.5 border rounded text-xs text-gray-600 hover:bg-gray-100">Cancel</button>
                    <button type="button" @click="generateWithAi()" class="bg-purple-600 text-white px-4 py-1.5 rounded text-xs font-medium hover:bg-purple-700 flex items-center space-x-1" :disabled="loading">
                        <span x-text="loading ? 'Generating...' : 'Generate Recipe'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function recipeApp() {
            return {
                showAiModal: false,
                aiPrompt: '',
                loading: false,
                form: {
                    title: '',
                    cuisine: '',
                    prep_time: '',
                    ingredients: '',
                    instructions: ''
                },
                async generateWithAi() {
                    if (!this.aiPrompt.trim()) return;
                    this.loading = true;

                    try {
                        const res = await fetch("{{ route('recipes.ai') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ prompt_ingredients: this.aiPrompt })
                        });

                        const data = await res.json();
                        this.form.title = data.title;
                        this.form.cuisine = data.cuisine;
                        this.form.prep_time = data.prep_time;
                        this.form.ingredients = data.ingredients;
                        this.form.instructions = data.instructions;

                        this.showAiModal = false;
                        this.aiPrompt = '';
                    } catch (e) {
                        alert("Failed to generate recipe.");
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</body>
</html>