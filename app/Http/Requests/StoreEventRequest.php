<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'event_name' => 'required|string|max:255',
            'event_type_id' => 'required|exists:event_types,id',
            'event_date_start' => 'required|date|after_or_equal:today',
            'event_date_end' => 'required|date|after_or_equal:event_date_start',
            'departments' => 'required|array|min:1',
            'courses' => 'required|array|min:1',
            'year_levels' => 'required|array|min:1',
            'signed_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        // Add fee validation if event type requires payment
        if ($this->event_type_id) {
            $eventType = \App\Models\EventType::find($this->event_type_id);
            if ($eventType && $eventType->requires_payment) {
                $rules['event_fee'] = 'required|numeric|min:0';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'signed_document.required' => 'Please upload the signed document.',
            'signed_document.mimes' => 'Document must be PDF, JPG, or PNG.',
            'signed_document.max' => 'Document size must not exceed 5MB.',
            'departments.required' => 'Please select at least one department.',
            'courses.required' => 'Please select at least one course.',
            'year_levels.required' => 'Please select at least one year level.',
        ];
    }
}