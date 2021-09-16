<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StrippedTrackResource extends JsonResource
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
            'created_at' => $this->created_at,
            'name' => $this->name,
            'explicit' => $this->explicit,
            'source' => $this->source,
            'streams' => $this->streams,
            'genres' => $this->genres,
            'duration' => $this->duration,
        ];
    }
}
