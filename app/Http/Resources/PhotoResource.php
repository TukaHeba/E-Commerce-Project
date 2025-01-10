<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoResource extends JsonResource
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
            'photo_name' => $this->photo_name,
            'photo_path' => $this->photo_path,
            'mime_type' => $this->mime_type,
            'photoable_id' => $this->photoable_id,
            'photoable_type' => $this->photoable_type,
        ];
    }
}
