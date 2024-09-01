<?php

namespace App\Http\Requests;

use App\Models\TourPackage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookingRequest extends FormRequest
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
            'tour_package_id' => [
                'required',
                'integer',
                Rule::exists('tour_packages', 'id')->where(function ($query) {
                    $query->where('published', true);
                }),
            ],
            'booking_date' => [
                'required',
                'date',
                'after:today',
                $this->validateBookingDate(),
            ],
            'number_of_participants' => [
                'required',
                'integer',
                'min:1',
                $this->validateMaxParticipants(),
            ],
            'pricing_tier_id' => [
                'required',
                'integer',
                $this->validatePricingTier(),
            ],
            'special_requirements' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'tour_package_id.required' => 'Please select a tour package.',
            'tour_package_id.exists' => 'The selected tour package is not available.',
            'booking_date.required' => 'Please select a booking date.',
            'booking_date.after' => 'The booking date must be a future date.',
            'number_of_participants.required' => 'Please specify the number of participants.',
            'number_of_participants.min' => 'There must be at least one participant.',
            'pricing_tier_id.required' => 'Please select a pricing tier.',
            'special_requirements.max' => 'Special requirements cannot exceed 500 characters.',
        ];
    }

    private function validateBookingDate()
    {
        return function ($attribute, $value, $fail) {
            $tourPackage = TourPackage::find($this->input('tour_package_id'));
            if ($tourPackage && $value > $tourPackage->end_date) {
                $fail('The booking date must be within the tour package dates.');
            }
        };
    }

    private function validateMaxParticipants()
    {
        return function ($attribute, $value, $fail) {
            $tourPackage = TourPackage::find($this->input('tour_package_id'));
            if ($tourPackage && $value > $tourPackage->max_participants) {
                $fail("The number of participants cannot exceed the tour package's maximum of {$tourPackage->max_participants}.");
            }
        };
    }

    private function validatePricingTier()
    {
        return function ($attribute, $value, $fail) {
            $tourPackage = TourPackage::find($this->input('tour_package_id'));
            if ($tourPackage) {
                $validTierIds = $tourPackage->pricingTiers()->pluck('id')->all();
                if (!in_array($value, $validTierIds)) {
                    $fail('The selected pricing tier is not valid for this tour package.');
                }
            }
        };
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'booking_date' => $this->transformDate($this->booking_date),
        ]);
    }

    private function transformDate($value)
    {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    }
}
