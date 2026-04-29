<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'alias' => $this->alias,
            'name' => $this->name,
            'surname1' => $this->surname1,
            'surname2' => $this->surname2,
            'email' => $this->email,
            'bio' => $this->bio,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'avatar_url' => $this->avatar_url,
            'guides_count' => $this->guides()->count(),
            'created_at' => $this->created_at?->toDateString()
        ];
    }
}
