<?php

namespace App\Http\Controllers;

use App\Http\Resources\AlbumCollection;
use App\Models\Album;
use App\Models\Track;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AlbumResource;

use function PHPUnit\Framework\isNull;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function single(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'cover' => 'required|string',
            'source' => 'required|string',
            'duration' => 'required|numeric',
            'cover_id' => 'required|string',
            'source_id' => 'required|string',
            'explicit' => 'required|boolean',
            'release_date' => 'date',
            'genres' => 'required|array',
        ]);
        $track = new Track;
        $track->fill([
            'id' => (string) Str::uuid()->getInteger(),
            'name' => $request->name,
            'explicit' => $request->explicit,
            'source' => $request->source,
            'genres' => $request->genres,
            'duration' => $request->duration,
            'source_id' => $request->source_id,
        ]);
        $album = new Album;
        $album->fill([
            'id' => (string) Str::uuid()->getInteger(),
            'name' => $request->name,
            'single' => true,
            'explicit' => $request->explicit,
            'cover' => $request->cover,
            'release_date' => !isNull($request->releaseDate) ? $request->releaseDate : now(),

            'cover_id' => $request->cover_id,
            'genres' => $request->genres,
        ]);
        DB::transaction(function () use ($album, $track, $request) {
            $album->artist()->associate($request->user()->type);
            $album->save();

            $album->tracks()->save($track);
        });

        return response()->json(
            new AlbumResource($album),
            201
        );
    }


    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'cover' => 'required|string',
            'cover_id' => 'required|string',
            'explicit' => 'required|boolean',
            'release_date' => 'date',
            'genres' => 'required|array',
        ]);

        $tracks = [];
        foreach ($request->tracks as $track) {

            $created = new Track;
            $created->fill([
                'id' => (string) Str::uuid()->getInteger(),
                'name' => $track["name"],
                'explicit' => $track["explicit"],
                'duration' => $track["duration"],
                'source' => $track["url"],
                'source_id' => $track["public_id"],
                'genres' => $track["genres"],
            ]);
            array_push($tracks, $created);
        }

        $album = new Album;
        $album->fill([
            'id' => (string) Str::uuid()->getInteger(),
            'name' => $request->name,
            'explicit' => $request->explicit,
            'cover' => $request->cover,
            'cover_id' => $request->cover_id,
            'release_date' => !isNull($request->releaseDate) ? $request->releaseDate : now(),

            'genres' => $request->genres,
        ]);
        DB::transaction(function () use ($album, $tracks, $request) {
            $album->artist()->associate($request->user()->type);
            $album->save();
            $album->tracks()->saveMany($tracks);
        });
        return response()->json(
            new AlbumResource($album),
            201
        );
    }

    public function getById($id)
    {

        $album = Album::find($id);
        return new AlbumResource($album);
    }
    public function newReleases()
    {
        $albums = Album::orderBy('release_date', 'desc')
            ->limit(12)
            ->get();
        return new AlbumCollection($albums);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function edit(Album $album)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Album $album)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Album  $album
     * @return \Illuminate\Http\Response
     */
    public function destroy(Album $album)
    {
        //
    }
}
