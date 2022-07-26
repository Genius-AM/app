<?php

namespace App\Http\Resources;

use App\Models\ExcursionCarTimetable;
use App\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteTime extends JsonResource
{
    public $preserveKeys = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'orders' => RouteTimeCount::collection($this->orders->groupBy(['time']))
        ];
    }
}
