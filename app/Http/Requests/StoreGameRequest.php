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
        $gameId = $this->route('game') ? $this->route('game')->id : null;

        return [
            'title' => 'required|string|max:255|unique:games,title,' . $gameId,
            'cover' => $this->hasFile('cover') ? 'image|mimes:webp|max:5120' : 'nullable|string',
        ];
    }
}
