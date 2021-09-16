<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Playlist;
use App\Models\NormalUser;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\PlaylistCollection;

class FavouritesController extends Controller
{
    public function getByType($id, $type, Request $request)
    {
        switch ($type) {
            case 'loved':
                $user = NormalUser::find($id);
                $favouriteTracks = $user->favouriteTracks->tracks()->paginate(10);
                return new TrackCollection($favouriteTracks);
                break;
            case 'playlist':
                if ($request->user() && $request->user()->type->id == $id) {
                    $playlists = $request->user()->type->playlists()
                        ->withCount('tracks')
                        ->paginate(10);
                    return new PlaylistCollection($playlists);
                }
                $playlists = Playlist::withCount('tracks')
                    ->where([['owner_id', '=', $id], ['private', '=', false]])
                    ->paginate(10);
                return new PlaylistCollection($playlists);

            default:
                return response()->json([], 404);

                break;
        }
    }
    public function addToFavouriteTracks(Request $request)
    {
        $request->validate([
            'track' => 'required|string'
        ]);
        try {
            $favouriteTracks = $request->user()->type->favouriteTracks;
            if (!in_array($request->track, array_column($favouriteTracks->tracks->toArray(), 'id'))) {
                $favouriteTracks->tracks()->attach($request->track);
                return response()->json(['message' => 'track was added to your favourites'], 200);
            }
            return response()->json(['message' => 'track already in favourites'], 200);
        } catch (\Throwable $th) {
            var_dump($th);
            return response()->json(['message' => 'an error has occured'], 500);
        }
    }
    public function removeFromFavouriteTracks(Request $request)
    {
        $request->validate([
            'track' => 'required|string'
        ]);
        try {
            $favouriteTracks = $request->user()->type->favouriteTracks;
            if (in_array($request->track, array_column($favouriteTracks->tracks->toArray(), 'id'))) {
                $favouriteTracks->tracks()->detach($request->track);
                return response()->json(['message' => 'track was removed from your favourites'], 200);
            }
            return response()->json(['message' => 'track not found in favourites'], 200);
        } catch (\Throwable $th) {
            var_dump($th);
            return response()->json(['message' => 'an error has occured'], 500);
        }
    }
}
