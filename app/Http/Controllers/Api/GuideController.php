<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guide;
use App\Http\Resources\GuideResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Requests\StoreGuideRequest;
use Illuminate\Support\Facades\Storage;

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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('guides', 'public');
            $data['image'] = '/storage/' . $path;
        }

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
    public function update(Request $request, Guide $guide) {
        $data = $request->only(['title', 'content', 'game_id']);

        if ($request->hasFile('image')) {
            // Borrar vieja
            if ($guide->image) {
                $oldPath = str_replace('/storage/', '', $guide->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('guides', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $guide->update($data);

        if ($request->has('categories')) {
            // sync() elimina las categorías viejas y pone las nuevas IDs enviadas
            $guide->categories()->sync($request->categories);
        }
        return response()->json($guide);
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

    public function myGuides()
    {
        // Obtenemos solo las guías del usuario que está logueado
        $guides = Guide::where('user_id', auth()->id())
            ->with(['user', 'game', 'categories'])
            ->latest()
            ->paginate(10);

        return GuideResource::collection($guides);
    }

    public function toggleFavorite(Guide $guide)
    {
        $user = auth()->user();
        $user->favorites()->toggle($guide->id);
        
        return response()->json(['message' => 'Favorite status updated']);
    }

    public function favorites()
    {
        $guides = auth()->user()->favorites()
            ->with(['user', 'game', 'categories'])
            ->latest()
            ->paginate(10);

        return GuideResource::collection($guides);
    }
}