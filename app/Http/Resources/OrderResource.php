<?php

namespace App\Http\Resources;

use App\Models\Address\City;
use App\Models\Address\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function ($data) {
                return [
                    'full_name' => $data->full_name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                ];
            }),
            'zone_id' => $this->zone_id,
            'postal_code' => $this->postal_code,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'order_number' => $this->order_number,
            'order_items' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'order_tracking' => OrderTrackingResource::collection($this->whenLoaded('orderTrackings')),
            'zone'=>$this->whenLoaded('zone'),
            'city'=>$this->whenLoaded('city'),
        ];
    }
}
