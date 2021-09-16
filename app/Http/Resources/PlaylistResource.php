<?php

namespace App\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaylistResource extends JsonResource
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
            'description' => $this->description,
            'tags' => $this->tags,
            'owner' =>  [
                'id' => $this->owner->id,
                'name' => $this->owner->user->name,
                'avatar' => $this->owner->user->avatar
            ],
            'private' => $this->private,
            'tracks' => new TrackCollection($this->tracks),
            'tracks_count' => $this->tracks->count(),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at

        ];
    }
}
