<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrescriptionRequest extends FormRequest
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
            'medication_name' => 'required|string',
            'dosage_amount' => 'nullable|string',
            'frequency' => 'nullable|string',
            'duration' => 'required|string',
            'doctor_name' => 'required|string',
            'patient_name' => 'required|string',
            'diagnosis_name' => 'required|string',
        ];
    }
}
