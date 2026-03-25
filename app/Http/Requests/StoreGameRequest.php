<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        // Esto permite que el título se repita solo si es la misma guía que estamos editando
        $guideId = $this->route('guide'); // Obtiene el ID de la URL

        return [
            'title' => 'required|string|max:255|unique:guides,title,' . $guideId,
            'content' => 'required',
            'game_id' => 'required|exists:games,id',
        ];
    }
}
