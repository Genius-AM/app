<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Route extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'price_men' => $this->price_men,
            'price_women' => $this->price_women,
            'price_kids' => $this->price_kids,
            'price' => $this->price,
        ];
    }
}
