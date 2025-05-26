<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReceptionerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'addres' => $this->addres,
            'hire_date' => $this->hire_date,
            'email' => $this->when(!is_null($this->email), $this->email),
            'phone' => $this->phone,
            'join_date' => $this->created_at->format('Y-m-d'),
        ];
    }
}
