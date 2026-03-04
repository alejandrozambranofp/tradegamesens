<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Http\Resources\GuideResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str; // Para generar el slug
use App\Http\Requests\StoreGuideRequest;

class GuideController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traemos las guías con los datos de su autor, juego y categorías asociadas
        $guides = Guide::with(['user', 'game', 'categories'])->latest()->paginate(10);
        return GuideResource::collection($guides);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuideRequest $request)
    {
        // Usamos los datos validados del StoreGuideRequest
        $data = $request->validated();
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = auth()->id() ?? 1; // ID 1 de momento
        $data['game_id'] = $request->game_id ?? 1;

        $guide = Guide::create($data);

        if ($request->has('categories')) {
            $guide->categories()->attach($request->categories);
        }

        return new GuideResource($guide->load(['game', 'categories']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Guide $guide)
    {
        return new GuideResource($guide->load(['user', 'game', 'categories']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Guide $guide, StoreGuideRequest $request)
    {
        $guide->update($request->validated());

        if ($request->title) {
            $guide->update(['slug' => Str::slug($request->title)]);
        }

        if ($request->has('categories')) {
            $guide->categories()->sync($request->categories);
        }

        return new GuideResource($guide);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guide $guide)
    {
        $guide->delete();
        return response()->noContent(); // Devuelve un 204 (Éxito sin contenido)
    }
}
