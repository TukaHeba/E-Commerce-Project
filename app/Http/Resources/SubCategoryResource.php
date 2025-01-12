<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
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
            'sub_category_name' => $this->sub_category_name,
            'main_categories' => MainCategoryResource::collection($this->whenLoaded('mainCategories')),
        ];
    }
}
