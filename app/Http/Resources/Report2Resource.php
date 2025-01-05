<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Report2Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'cart_id' => $this->cart_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => Carbon::parse($this->created_at)->format('d F Y, h:i A')
        ];
    }
}
