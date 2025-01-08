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
            'Product id'             => $this->id,
            'Product Name'           => $this->name,
            'Product Description'    => $this->description,
            'Product Price'          => $this->price,
            'Product Quantity' => $this->product_quantity,
            'Product Category'       => [
                'main category' => $this->whenLoaded('mainCategory', fn() => $this->mainCategory->main_category_name),
                'sub category'  => $this->whenLoaded('subCategory', fn() => $this->subCategory->sub_category_name),
            ],
            'Average rating' => $this->averageRating() ?? 0,
        ];
    }
}
