<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var Cell */
        $cell = $this->cell;
        $sector = $cell->sector;
        $district = $sector->district;
        $address = $district->province->name ."/". $district->name ."/". $sector->name ."/". $cell->name;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'status' => $this->status,
            'address' => $address,
            'category' => $this->category->name,
            "added_by"=> $this->addedBy->only(["id", "name"]),
            'created' => $this->created_at->diffForHumans(),
            'additional_info' => $this->additional_info,
            "image"=> asset($this->getImagePublicLink()),
        ];
    }
}
