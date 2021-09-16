<?php

namespace App\Models;

use App\Models\Track;
use App\Models\NormalUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tags', 'cover', 'description', 'private'];
    protected $casts = [
        'tags' => 'array',
    ];
    protected $attributes = [
        'private' => false
    ];
    public function tracks()
    {
        return $this->belongsToMany(Track::class);
    }
    public function owner()
    {
        return $this->belongsTo(NormalUser::class);
    }
}
