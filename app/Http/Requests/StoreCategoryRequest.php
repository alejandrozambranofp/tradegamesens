<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            // Cambiamos a nullable y ajustamos el unique para que ignore el ID actual al editar
            'slug' => 'nullable|unique:categories,slug,' . $this->route('category')?->id,
            'color' => 'nullable|regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'
        ];
    }
}
