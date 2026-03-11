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
    public function update(StoreGuideRequest $request, Guide $guide)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($request->title);

        $guide->update($data);

        if ($request->has('categories')) {
            $guide->categories()->sync($request->categories);
        }

        return new GuideResource($guide->load(['game', 'categories']));
    }

    /**
     * Elimina una guía.
     */
    public function destroy(Guide $guide)
    {
        $guide->delete();
        return response()->noContent();
    }
}