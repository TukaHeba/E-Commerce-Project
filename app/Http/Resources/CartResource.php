<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user'=>$this->whenLoaded('user',function ($data){
                return [
                    'full_name'=>$data->full_name,
                    'email'=>$data->email
                ];
            }),
            'cart_items' => CartItemResource::collection($this->whenLoaded('cartItems')),
        ];
    }
}
