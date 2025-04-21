<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'patient_info' => new DiagnosisResource($this->whenLoaded('Patinets')),
            'Doctors_info' => new DoctorResource($this->whenLoaded('Doctors')),
            'Diagnosises_info' => new DiagnosisResource($this->whenLoaded('Diagnosises')),
            'Prescriptions_info' => new PrescriptionResource($this->whenLoaded('Prescriptions'))
        ];
    }
}
