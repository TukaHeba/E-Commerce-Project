<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'old_status' => $this->old_status ?? 'not assigned',
            'new_status' => $this->new_status,
            'changed_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
