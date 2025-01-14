<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'gender' => $this->is_male  === 1 ? 'Male' : 'Female',
            'birthdate' => $this->birthdate,
            'telegram_user_id' => $this->telegram_user_id,
            'avatar' => $this->avatar ? $this->avatar->photo_path : null,

        ];
    }
}
