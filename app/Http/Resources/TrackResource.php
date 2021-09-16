<?php

namespace App\Http\Resources;

use App\Http\Resources\ArtistResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackResource extends JsonResource
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
            'source' => $this->source,
            'streams' => $this->streams,
            'duration' => $this->duration,
            'explicit' => $this->explicit,
            'genres' => $this->genres,
            'album' => [
                'id' => $this->album->id,
                'name' => $this->album->name,
                'cover' => $this->album->cover,
            ],
            'artist' => [
                'id' => $this->album->artist->id,
                'name' => $this->album->artist->user->name
            ]
        ];
    }
}
