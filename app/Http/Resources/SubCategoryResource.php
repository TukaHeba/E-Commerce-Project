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
            'sub category id' => $this->id,
            'sub category name' => $this->sub_category_name, 
            'main categories' => $this->mainCategories ? $this->mainCategories->map(function ($mainCategory) {
                return [
                    'id' => $mainCategory->id,
                    'name' => $mainCategory->main_category_name,
                ];
            }) : [],
        ];
    }
}
