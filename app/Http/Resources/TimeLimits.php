<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeLimits extends JsonResource
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
            'route_id' => $this->resource['route']->route_id,
            'company_id' => $this->resource['company']->id,
            'company' => iconv_substr($this->resource['company']->name, 0, 4, 'UTF-8'),
            'DateTime' => $this->resource['DateTime'],
            'price' => $this->resource['route']->price,
            'duration' => $this->resource['route']->duration,
            'men' => $this->resource['men'],
            'women' => $this->resource['women'],
            'kids' => $this->resource['kids']
        ];
    }
}
