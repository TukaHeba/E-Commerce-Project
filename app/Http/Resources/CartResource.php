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
            'cart_items' => CartItemResource::collection($this->whenLoaded('cartItems')),
            // 'user_id'=>$this->user_id,
            // 'user'=>new UserResource($this->whenLoaded('user')),
            // 'cart_items' => $this->whenLoaded('cartItems', function ($items) {
            //     return $this->cartItems->map(function ($item) {
            //         return new CartItemResource($item);
            //     });
            // }, []),
            // 'updated_at'=>$this->updated_at,
            // 'created_at'=>$this->created_at
        ];
    }
}
