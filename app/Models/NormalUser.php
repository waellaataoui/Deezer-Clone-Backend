<?php

namespace App\Models;

use App\Models\User;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NormalUser extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->morphOne(User::class, 'model');
    }
    public function playlists()
    {
        return $this->hasMany(Playlist::class, 'owner_id');
    }
    public function favouriteTracks()
    {
        return $this->hasOne(Playlist::class, 'owner_id');
    }
}
