<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::query()->where('is_active', true);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->boolean('favorite')) {
            $query->where('is_favorite', true);
        }

        if ($request->boolean('popular')) {
            $query->where('is_popular', true);
        }

        $games = $query->orderBy('name')->get([
            'id', 'name', 'slug', 'banner_image', 'logo_image', 'is_favorite', 'is_popular',
        ]);

        return response()->json(['success' => true, 'data' => $games]);
    }

    public function show(string $slug)
    {
        $game = Game::where('slug', $slug)
            ->where('is_active', true)
            ->with(['categories' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }, 'categories.products' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();

        return response()->json(['success' => true, 'data' => $game]);
    }
}