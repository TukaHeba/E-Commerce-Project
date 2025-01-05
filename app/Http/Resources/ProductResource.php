<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'description'    => $this->description,
            'price'          => $this->price,
            'product_quantity' => $this->product_quantity,
            'category'       => [
                'main_category' => $this->whenLoaded('mainCategory', fn() => $this->mainCategory->main_category_name),
                'sub_category'  => $this->whenLoaded('subCategory', fn() => $this->subCategory->sub_category_name),
            ],
            // 'category'       => $this->maincategory_subcategory_id,
            'average rating' => $this->averageRating() ?? 0,
        ];
    }
}
