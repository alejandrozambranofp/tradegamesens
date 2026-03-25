<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Http\Resources\GuideResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreGuideRequest;

class GuideController extends Controller
{
    /**
     * Lista todas las guías con sus relaciones.
     */
    public function index()
    {
        $guides = Guide::with(['user', 'game', 'categories'])->latest()->paginate(10);
        return GuideResource::collection($guides);
    }

    /**
     * Crea una nueva guía generando el slug automáticamente.
     */
    public function store(StoreGuideRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($request->title);
        $data['user_id'] = auth()->id() ?? 1;

        $guide = Guide::create($data);

        if ($request->has('categories')) {
            $guide->categories()->attach($request->categories);
        }

        return new GuideResource($guide->load(['game', 'categories']));
    }

    /**
     * Muestra una guía específica buscando por ID o por Slug.
     */
    public function show($idOrSlug)
    {
        $guide = Guide::where('id', $idOrSlug)
            ->orWhere('slug', $idOrSlug)
            ->with(['user', 'game', 'categories'])
            ->firstOrFail();

        return new GuideResource($guide);
    }

    /**
     * Actualiza una guía existente.
     */
    public function update(Request $request, Guide $guide)
    {
        // Permitir si es Admin (ID 1) O si es el creador de la guía
        if (auth()->id() !== 1 && auth()->id() !== $guide->user_id) {
            return response()->json(['message' => 'No puedes editar guías de otros'], 403);
        }

        $guide->update($request->all());
        return response()->json(['data' => $guide]);
    }

    public function destroy(Guide $guide)
    {
        // Misma lógica para eliminar
        if (auth()->id() !== 1 && auth()->id() !== $guide->user_id) {
            return response()->json(['message' => 'No puedes borrar guías de otros'], 403);
        }

        $guide->delete();
        return response()->json(null, 204);
    }
}