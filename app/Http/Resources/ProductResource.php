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
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'product_quantity' => $this->product_quantity,
            'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'main_category' => new MainCategoryResource($this->whenLoaded('mainCategory')),
            'average_rating' => $this->averageRating() ?? 0,
            'favorites_count' => $this->favorites_count ?? 0,
            'photos' => PhotoResource::collection($this->whenLoaded('photos')),
            'ratings' => RateResource::collection($this->whenLoaded('rates')),

            // 'Product id'             => $this->id,
            // 'Product Name'           => $this->name,
            // 'Product Description'    => $this->description,
            // 'Product Price'          => $this->price,
            // 'Product Quantity' => $this->product_quantity,
            // 'Product Category'       => [
            //     'main category' => $this->whenLoaded('mainCategory', fn() => $this->mainCategory->main_category_name),
            //     'sub category'  => $this->whenLoaded('subCategory', fn() => $this->subCategory->sub_category_name),
            // ],
            // 'Average rating' => $this->averageRating() ?? 0,
        ];
    }
}
