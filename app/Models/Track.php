<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'explicit',
        'genres',
        'duration',
        'source',
        'source_id',
        'streams'
    ];
    protected $attributes = [
        'streams' => 0,
    ];
    protected $casts = [
        'genres' => 'array',
    ];
    /**
     * Get the track's album.
     */
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
