<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubMainCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product id' => $this->id,
            'sub category name' => $this->sub_category_name, 
            'main category name' => $this->main_category_name, 
        ];
    }
}
