<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->Name ?? null,
            'Degree' => $this->Degree,
            'Specialization' => $this->Specialization,
            'email' => $this->when(!is_null($this->email), $this->email),
            'phone' => $this->phone,
            'Available' => $this->Available,
            'join_date' => $this->created_at->format('Y-m-d'),


        ];
    }
}
