<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
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
    public function rules(): array
    {
        return [
        'rating_type'  => 'required|integer|between:1,7',
        'entity_id'    => 'required|integer',
        'rating'       => 'required|integer|min:1|max:5',
        'comment'      => 'nullable|string',
        ];
    }
}
