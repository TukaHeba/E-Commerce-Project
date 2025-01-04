<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Report1Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id'=>$this->user_id,
            'shipeed_address'=>$this->shipped_address,
            'status'=>$this->status ,
            'total_price'=>$this->total_price ,
            'created_at'=>Carbon::parse($this->created_at)->format('Y M d, H:i:s')
        ];
    }
}
