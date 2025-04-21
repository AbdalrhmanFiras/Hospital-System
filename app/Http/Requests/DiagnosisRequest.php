<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiagnosisRequest extends FormRequest
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
            'diseases_name' => 'required|string',
            'diseases' => 'required|string|in:infectious diseases, deficiency diseases, hereditary diseases, physiological diseases',
            'diagnoses' => 'required|string|in:clinical,medical',
            'allergies' => 'nullable|text',
            'doctor_name' => 'required|string',
            'patient_name' => 'required|string',
        ];
    }
}
