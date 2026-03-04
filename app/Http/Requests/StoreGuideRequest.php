<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Permitimos que el usuario haga la petición
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // El título y contenido siguen siendo obligatorios
            'title' => 'required|string|max:255',
            'content' => 'required|string',

            // IMPORTANTE: Cambiamos a 'nullable' para que no dé error en el navegador
            // El slug lo generaremos automáticamente en el Controller
            'slug' => 'nullable|string|max:255|unique:guides,slug',

            // El game_id es nullable para que funcione tu parche de "game_id ?? 1"
            'game_id' => 'nullable|exists:games,id',

            // Las categorías deben ser un array de IDs que existan
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id'
        ];
    }

    /**
     * Mensajes de error personalizados (Opcional)
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título de la guía es obligatorio.',
            'content.required' => 'Debes escribir el contenido de la guía.',
            'game_id.exists' => 'El juego seleccionado no es válido.'
        ];
    }
}