<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'title' => $this->title, // Asegúrate de que diga 'title' y no 'name'
            'slug'  => $this->slug,
        ];
    }
    
}
