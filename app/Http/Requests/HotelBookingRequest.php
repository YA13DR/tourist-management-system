<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HotelBookingRequest extends FormRequest
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
            'roomType_id'=>'required',
            'checkInDate'=>'after_or_equal:today',
            'numberOfRooms'=>'required',
            'numberOfGuests'=>'required',
            'numberOfDays' => 'required|integer|min:1'
        ];
    }
}
