<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class DoctorRequest extends FormRequest
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
            'name' => 'required|string',
            'Specialization' => 'required|string',
            'Degree' => 'required|string|in:Bachelor,Master,Doctoral',
            'phone' => 'required|string|min:10',
            'email' => 'required|email',
            'price' => 'required|numeric|decimal:2,3,4',
            'password' => 'required|string|min:8|confirmed'
        ];
    }
}
