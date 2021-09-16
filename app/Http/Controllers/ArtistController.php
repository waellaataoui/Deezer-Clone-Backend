<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Artist;
use Illuminate\Http\Request;
use App\Http\Resources\AlbumCollection;

class ArtistController extends Controller
{

    public function getArtistById($id)
    {
        $artist = Artist::find($id);
        if ($artist) {
            return  $artist->user;
        } else return response()->json([], 404);
    }
    public function getAlbums($id)
    {
        $artist = Artist::find($id);
        if (!$artist) {
            return response()->json([], 404);
        } else {
            return new AlbumCollection($artist->albums);
        }
    }
}
