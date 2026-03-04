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
            // IMPORTANTE: Cambiamos a 'nullable' para que no dé error en el navegador
            // El slug lo generaremos automáticamente en el Controller
            'slug' => 'required|string|max:255|unique:guides,slug',
            'content' => 'required',

            

            // El game_id es nullable para que funcione tu parche de "game_id ?? 1"
            'game_id' => 'required|exists:games,id',

            // Las categorías deben ser un array de IDs que existan
            'categories' => 'array'
        ];
    }

}