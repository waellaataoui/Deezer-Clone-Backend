<?php

namespace App\Http\Controllers;

use App\Http\Resources\PlaylistCollection;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlaylistsController extends Controller
{

    public function createPlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'tags' => 'array',
            'description' => 'string',
            'private' => 'boolean',
        ]);
        $playlist =  new Playlist();
        $tags = $request->tags;
        if ($tags)  array_walk($tags, function (&$value) {
            $value = strtolower($value);
        });
        $playlist->fill([
            'name' => strtolower($request->name),
            'tags' => $tags,
            'description' => $request->description,
            'private' => $request->private || false,
        ]);
        DB::transaction(function () use ($playlist, $request) {
            $playlist->owner()->associate($request->user()->type);
            $playlist->save();
        });
        return response()->json(
            $playlist
        );
    }

    public function addTracks(Request $request)
    {
        $request->validate([
            'playlistId' => 'required|Numeric',
            'tracks' => 'array|min:1'
        ]);

        $playlist = Playlist::find($request->playlistId);
        if (!$playlist) return response()->status(404);
        if ($playlist->owner->id !== $request->user()->type->id)
            return response('Unauthorized.', 401);
        $current_tracks = array_column($playlist->tracks->toArray(), 'id');
        $duplicate_tracks = array_intersect($current_tracks, $request->tracks);
        if (count($duplicate_tracks) > 0) {
            if (count($request->tracks) == 1)
                return response()->json(['message' => 'This track has already been added to the playlist'], 400);
            $new_tracks = array_diff($request->tracks, $duplicate_tracks);
            if (count($new_tracks) == 0) return response()->json(['message' => 'These tracks has already been added to the playlist'], 400);
            $playlist->tracks()->attach($new_tracks);
            $playlist->touch();
        } else {
            $playlist->tracks()->attach($request->tracks);
            $playlist->touch();
        }

        return response()->json(['message' => 'success'], 200);
    }
    public function removeTracks(Request $request)
    {
        $request->validate([
            'playlistId' => 'required|Numeric',
            'tracks' => 'array|min:1'
        ]);

        $playlist = Playlist::find($request->playlistId);
        if (!$playlist) return response()->json(['message' => 'No such playlist'], 404);
        if ($playlist->owner->id !== $request->user()->type->id)
            return response('Unauthorized.', 401);

        $playlist->tracks()->detach($request->tracks);
        $playlist->touch();

        return response()->json(['message' => 'success'], 200);
    }


    public function getById($id, Request $request)
    {

        $playlist = Playlist::with('owner')->find($id);
        if (!$playlist || ($playlist->private && $request->user() && $request->user()->type->id !== $playlist->owner->id)) return response()->json(['message' => 'Playlist doesnt exist'], 404);

        return new PlaylistResource($playlist);
    }
    public function getPlaylists(Request $request)

    {
        $tags = $request->query('tags') ? explode(',', $request->query('tags')) : null;
        array_walk($tags, function (&$value) {
            $value = strtolower($value);
        });
        $playlists = Playlist::when($tags, function ($query, $tags) {
            return $query->whereJsonContains('tags', $tags);
        })->where([['private', '=', false], ['owner_id', '=', '1']])
            // ->orderBy('popularity','desc')
            ->paginate(12);
        // $playlists = \App\Models\Playlist::whereIn('tags', ['Pop']);

        return new PlaylistCollection($playlists);
    }


    public function myPlaylists(Request $request)
    {
        $playlists = $request->user()->type->playlists;
        // ->toArray();
        // return response()->json($playlists, 200);
        // foreach ($playlists as $key => $playlist) {
        //     // var_dump($request->user()->type->favouriteTracks->id);
        //     if ($request->user()->type->favouriteTracks->id == $playlist['id']) {
        //         $playlists[$key]['name'] = 'Favourite Tracks';
        //     }
        // }
        return $playlists;
        // var_dump($playlists);
        // return new PlaylistCollection($playlists);
    }
}
