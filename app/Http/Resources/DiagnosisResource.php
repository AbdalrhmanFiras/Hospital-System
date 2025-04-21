<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiagnosisResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'diseases_name' => $this->diseases_name,
            'diseases' => $this->diseases,
            'diagnoses' => $this->diagnoses,
            'allergies' => $this->when(!is_null($this->allergies), $this->allergies),
            'doctor_id' => $this->doctor_id,
            'patient_id' => $this->patient_id,

        ];
    }
}
