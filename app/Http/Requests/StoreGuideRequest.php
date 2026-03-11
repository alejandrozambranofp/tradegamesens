<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGuideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el ID de la guía de la ruta para ignorarlo en la validación unique
        $guideId = $this->route('guide') ? $this->route('guide')->id : null;

        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'game_id' => 'required|exists:games,id',
            'categories' => 'nullable|array',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                // Ignora el ID actual para permitir editar sin cambiar el slug
                Rule::unique('guides', 'slug')->ignore($guideId),
            ],
        ];
    }
}