<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
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
            'name' => $this->name,
            'age' => $this->age,
            'gender' => $this->gender,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->when(!is_null($this->email), $this->email),
            'registratoin_date' => $this->created_at->format('Y-m-d h:i:s')

        ];


    }
}
