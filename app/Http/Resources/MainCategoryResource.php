<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MainCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'main category id' => $this->id,
            'main category name' => $this->main_category_name, 
            'sub_categories' => $this->subCategories ? $this->subCategories->map(function ($subCategory) {
                return [
                    'id' => $subCategory->id,
                    'name' => $subCategory->sub_category_name,
                ];
            }) : [],
        ];
    }
}
