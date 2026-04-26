<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameResource;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GameController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        // Devolvemos una colección de recursos
        return GameResource::collection(Game::all());
    }

    public function store(StoreGameRequest $request): GameResource
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('guides', 'public');
            $data['cover'] = asset(Storage::url($path));
        }

        $game = Game::create($data);

        return new GameResource($game);
    }

    public function show(Game $game): GameResource
    {
        return new GameResource($game);
    }

    public function update(StoreGameRequest $request, Game $game): GameResource
    {
        $data = $request->validated();
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($request->hasFile('cover')) {
            // Opcional: Borrar la vieja si existe y es local
            if ($game->cover && str_contains($game->cover, '/storage/guides/')) {
                $oldPath = 'guides/' . basename($game->cover);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('cover')->store('guides', 'public');
            $data['cover'] = asset(Storage::url($path));
        }

        $game->update($data);

        return new GameResource($game);
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return response()->json(['message' => 'Game deleted successfully'], 204);
    }
}
