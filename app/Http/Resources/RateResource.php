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
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'created_at' => $this->created_at->toDateTimeString(),

            // 'rate id'        => $this->id,
            // 'user name'      => $this->whenLoaded('user', function() { return $this->user->full_name;}),
            // 'product name'   => $this->whenLoaded('product', function() { return $this->product->name;}),
            // 'rating'         => $this->rating,
            // 'review'         => $this->review,
        ];
    }
}
