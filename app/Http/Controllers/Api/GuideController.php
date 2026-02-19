<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guide;
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
        $guides = Guide::with(['user', 'game', 'categories'])->latest()->get();
        return response()->json($guides);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGuideRequest $request)
    {
        // Creamos la guía
        $guide = Guide::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title), // Convierte "Mi Guía" en "mi-guia"
            'content' => $request->content,
            'game_id' => $request->game_id,
            'user_id' => 1 // PONEMOS 1 DE MOMENTO (Luego será: auth()->id() cuando haya login)
        ]);

        // Si el usuario marcó categorías, las guardamos en la tabla pivote
        if ($request->has('categories')) {
            $guide->categories()->attach($request->categories);
        }

        return response()->json([
            'message' => 'Guía creada correctamente',
            'guide' => $guide->load(['game', 'categories'])
        ], 201); // 201 significa "Creado"
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // findOrFail dará error 404 automático si la guía no existe
        $guide = Guide::with(['user', 'game', 'categories'])->findOrFail($id);
        return response()->json($guide);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $guide = Guide::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'game_id' => 'sometimes|exists:games,id',
            'categories' => 'array'
        ]);

        // Actualizamos título y contenido
        $guide->update([
            'title' => $request->title ?? $guide->title,
            'slug' => $request->title ? Str::slug($request->title) : $guide->slug,
            'content' => $request->content ?? $guide->content,
            'game_id' => $request->game_id ?? $guide->game_id,
        ]);

        // Sincronizamos las categorías (borra las viejas y pone las nuevas)
        if ($request->has('categories')) {
            $guide->categories()->sync($request->categories);
        }

        return response()->json([
            'message' => 'Guía actualizada',
            'guide' => $guide->load(['categories'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $guide = Guide::findOrFail($id);
        $guide->delete();

        return response()->json([
            'message' => 'Guía eliminada correctamente'
        ]);
    }
}
