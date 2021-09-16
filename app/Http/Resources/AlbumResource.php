<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ArtistResource;
use App\Http\Resources\StrippedTrackCollection;

class AlbumResource extends JsonResource
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
            'single' => $this->single,
            'cover' => $this->cover,
            'name' => $this->name,
            'explicit' => $this->explicit,
            'release_date' => $this->release_date,
            'created_at' => $this->created_at,
            'genres' => $this->genres,
            'artist' => new ArtistResource($this->artist),
            'tracks' => new TrackCollection($this->tracks)
        ];
    }
}
