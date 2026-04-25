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
        // Solo guías publicadas para el público general
        $guides = Guide::where('status', 'published')
            ->with(['user', 'game', 'categories'])
            ->latest()
            ->paginate(50);
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

        // Procesar imágenes base64 pegadas en el contenido
        if (isset($data['content'])) {
            $data['content'] = $this->processContentImages($data['content']);
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
            ->with(['user', 'game', 'categories', 'ratings.user'])
            ->firstOrFail();

        return new GuideResource($guide);
    }

    /**
     * Actualiza una guía existente.
     */
    public function update(Request $request, Guide $guide)
    {
        $data = $request->only(['title', 'content', 'game_id', 'difficulty']);
        // Siempre que se edita una guía, vuelve a estado pendiente de revisión
        $data['status'] = 'pending';

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

        // Procesar imágenes base64 pegadas en el contenido
        if (isset($data['content'])) {
            $data['content'] = $this->processContentImages($data['content']);
        }

        if ($request->has('title')) {
            $data['slug'] = Str::slug($request->title);
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

    /**
     * Devuelve las 4 guías más votadas basándose en 30% promedio y 70% cantidad de votos
     */
    public function topRated()
    {
        $guides = Guide::where('status', 'published')
            ->with(['user', 'game', 'categories'])
            ->withCount('ratings')
            ->withAvg('ratings', 'score')
            ->get()
            ->sortByDesc(function ($guide) {
                // Normalización aproximada para el cálculo
                // Asumimos un máximo de 5 estrellas (0 a 1)
                $avgNormalized = ($guide->ratings_avg_score ?? 0) / 5;

                // Normalizamos los votos (ej. a partir de 20 votos = 100% de fuerza)
                $countNormalized = min(1, ($guide->ratings_count ?? 0) / 20);

                // 30% de peso al promedio, 70% de peso a la cantidad de votos
                return ($avgNormalized * 0.3) + ($countNormalized * 0.7);
            })
            ->take(4)
            ->values();

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
            ->where('status', 'published')
            ->with(['user', 'game', 'categories'])
            ->latest()
            ->paginate(10);

        return GuideResource::collection($guides);
    }

    /**
     * Métodos para Administración
     */
    public function adminIndex()
    {
        $guides = Guide::with(['user', 'game', 'categories', 'ratings.user'])
            ->latest()
            ->paginate(20);
        return GuideResource::collection($guides);
    }

    public function updateStatus(Request $request, Guide $guide)
    {
        $request->validate([
            'status' => 'required|in:pending,published,rejected'
        ]);

        $guide->update(['status' => $request->status]);

        return new GuideResource($guide->load(['user', 'game', 'categories']));
    }

    /**
     * Procesa el contenido HTML buscando imágenes en base64, las guarda en disco
     * y devuelve el HTML con las URLs de las imágenes.
     */
    private function processContentImages($content)
    {
        if (empty($content)) return $content;

        // Buscamos patrones de src="data:image/(png|jpg|jpeg|gif|webp);base64,..."
        return preg_replace_callback('/src="data:image\/(\w+);base64,([^"]+)"/', function($matches) {
            $extension = $matches[1];
            $base64Data = $matches[2];
            
            // Decodificar la imagen
            $data = base64_decode($base64Data);
            if (!$data) return $matches[0]; // Si falla, dejamos el original

            // Generar un nombre único para la imagen
            $fileName = 'guide_img_' . uniqid() . '_' . time() . '.' . $extension;
            $path = 'guides/' . $fileName;

            // Guardar en el disco 'public'
            Storage::disk('public')->put($path, $data);

            // Devolver la nueva URL para el atributo src
            return 'src="' . Storage::url($path) . '"';
        }, $content);
    }
}
