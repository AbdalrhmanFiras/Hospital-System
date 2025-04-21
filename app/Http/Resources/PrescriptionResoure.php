<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResoure extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'diagnosis_id' => $this->diagnosis_id,
            'diagnosis' => new DiagnosisResource($this->whenLoaded('diagnosis')),
            'medication_name' => $this->medication_name,
            'dosage_amount' => $this->when(!is_null($this->dosage_amount), $this->dosage_amount),
            'frequency' => $this->when(!is_null($this->frequency), $this->frequency),
            'duration' => $this->duration,
            'written_date' => $this->created_at->format('Y-m-n h:i:s')

        ];
    }
}
