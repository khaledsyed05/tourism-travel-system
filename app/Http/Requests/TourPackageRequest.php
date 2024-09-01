<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TourPackageRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'destination_id' => 'required|exists:destinations,id',
            'itinerary' => 'required|array',
            'itinerary.*.day' => 'required|integer|min:1',
            'itinerary.*.description' => 'required|string',
        ];
    }
}
