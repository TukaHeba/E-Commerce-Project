<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'rate id'        => $this->id,
            'user name'      => $this->whenLoaded('user', function() { return $this->user->full_name;}),
            'product name'   => $this->whenLoaded('product', function() { return $this->product->name;}),
            'rating'         => $this->rating,
            'review'         => $this->review,
        ];
    }
}
