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
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'image_url' => $this->image_url,
            'is_favorite' => auth()->check() ? $this->favoritedBy()->where('user_id', auth()->id())->exists() : false,
            'created_at' => $this->created_at->format('d/m/Y'), // Fecha bonita
            // Cargamos las relaciones solo si están disponibles
            'user_id'    => $this->user_id,
            'user' => $this->whenLoaded('user'),
            'game' => $this->whenLoaded('game'),
            'categories' => $this->whenLoaded('categories'),
        ];
    }
}
