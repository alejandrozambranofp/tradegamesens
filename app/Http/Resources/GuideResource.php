<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = auth()->id();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'difficulty' => $this->difficulty,
            // Mantener la versión robusta que funciona
            'is_favorite' => $userId ? $this->favoritedBy()->where('favorite_guide_user.user_id', $userId)->exists() : false,
            'created_at' => $this->created_at ? $this->created_at->format('d/m/Y') : null,

            // Restaurar sistema de valoraciones (Ratings)
            'ratings_count' => $this->whenCounted('ratings', fn($count) => $count, 0),
            'rating' => round($this->ratings_avg_score ?? 0, 1),
            'ratings' => RatingResource::collection($this->whenLoaded('ratings')),

            // Relaciones
            'user_id'    => $this->user_id,
            'game_id'    => $this->game_id,
            'user' => $this->whenLoaded('user'),
            'game' => $this->whenLoaded('game'),
            'categories' => $this->whenLoaded('categories'),
        ];
    }
}
