<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Http\Requests\StoreGameRequest;
use App\Http\Resources\GameResource;
use Illuminate\Support\Str;
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

        $game = Game::create($data);

        return new GameResource($game);
    }

    public function show(Game $game): GameResource
    {
        return new GameResource($game);
    }

    public function update(StoreGameRequest $request, Game $game): GameResource
    {
        // Nota: Si quieres reglas distintas para update, crea un UpdateGameRequest
        $data = $request->validated();
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
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