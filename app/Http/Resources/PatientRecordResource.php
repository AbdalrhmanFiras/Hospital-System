<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\PatientResource;
use App\Http\Resources\DoctorResource;

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
            'patients_info' => new PatientResource($this->whenLoaded('Patinets')),
            'doctors_info' => new DoctorResource($this->whenLoaded('doctors')),
            'diagnosis_info' => new DiagnosisResource($this->whenLoaded('diagnosis')),
            'prescription_info' => new PrescriptionResource($this->whenLoaded('prescription')),
        ];
    }
}
