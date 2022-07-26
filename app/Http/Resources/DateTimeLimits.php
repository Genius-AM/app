<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DateTimeLimits extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'men' => $this->resource['men'],
            'women' => $this->resource['women'],
            'kids' => $this->resource['kids']
        ];
    }
}
