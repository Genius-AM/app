<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SeaTimeLimits extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'route_id' => $this->resource['route']->id,
            'DateTime' => $this->resource['DateTime'],
            'price' => $this->resource['route']->price,
            'duration' => $this->resource['route']->duration,
            'men' => $this->resource['men'],
            'women' => $this->resource['women'],
            'kids' => $this->resource['kids'],
            'time_id' => $this->resource['time_id'],
        ];
    }
}
