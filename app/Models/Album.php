<?php

namespace App\Models;

use App\Models\Artist;
use Illuminate\Support\Facades\Date;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Album extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'genres',
        'explicit',
        'cover',
        'cover_id',
        'single',
        'release_date'

    ];
    protected $attributes = [
        'single' => false,
    ];
    protected $casts = [
        'genres' => 'array',
        'release_date' => 'datetime',
    ];
    public function tracks()
    {
        return $this->hasMany(Track::class);
    }
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}
