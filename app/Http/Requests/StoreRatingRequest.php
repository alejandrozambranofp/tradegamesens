<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guide_id' => 'required|exists:guides,id',
            'score'    => 'required|integer|min:1|max:5',
            'comment'  => 'nullable|string|max:1000',
        ];
    }
}
