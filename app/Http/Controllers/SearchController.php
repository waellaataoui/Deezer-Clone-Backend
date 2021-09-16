<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Album;
use App\Models\Track;
use App\Models\Artist;
use App\Models\Playlist;
use Illuminate\Http\Request;
use App\Http\Resources\AlbumCollection;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\ArtistCollection;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\PlaylistCollection;

class SearchController extends Controller
{
    public function autoComplete(Request $request)
    {
        $results = [];
        $query = strtolower($request->query('keywords'));
        if (!$query) return response([]);
        //can rank them by popularity or streams
        $tracks = Track::select('name')->where('name', 'ilike', '%' . $query . '%')
            ->limit(2)
            ->get();
        array_push($results, ...$tracks);
        $artists = User::whereHasMorph(
            'type',
            Artist::class,
            function (Builder $builder) use ($query) {
                $builder->where('name', 'ilike', '%' . $query . '%');
            }
        )->limit(2)
            ->get();
        array_push($results, ...$artists);
        $albums = Album::select('name')->where('name', 'ilike', '%' . $query . '%')
            ->limit(2)
            ->get();
        array_push($results, ...$albums);
        $playlists = Playlist::select('name')->where([['name', 'ilike', '%' . $query . '%'], ['private', '=', false]])
            ->limit(2)
            ->get();
        array_push($results, ...$playlists);

        return  array_values(array_unique(array_column($results, 'name')));
    }
    public function search($keywords, $type = null, Request $request)
    {
        $keywords = strtolower($keywords);
        if (!$type) {
            $results = [];
            $tracks = Track::whereHas('album.artist.user', function (Builder $query) use ($keywords) {
                $query->where('name', 'ilike', '%' . $keywords . '%');
            })->orWhere(
                'name',
                'ilike',
                '%' . $keywords . '%',
            )
                ->limit(6)
                ->get();
            $results['tracks'] = new TrackCollection($tracks);
            $albums = Album::whereHas('artist.user', function (Builder $query) use ($keywords) {
                $query->where('name', 'ilike', '%' . $keywords . '%');
            })->orWhere(
                'name',
                'ilike',
                '%' . $keywords . '%',
            )
                ->limit(4)
                ->get();
            $results['albums'] = new AlbumCollection($albums);
            $artists = Artist::whereHas('user', function (Builder $query) use ($keywords) {
                $query->where('name', 'ilike', '%' . $keywords . '%');
            })->limit(4)->get();
            $results['artists'] = new ArtistCollection($artists);
            $playlists = Playlist::withCount('tracks')
                ->where([['name', 'ilike', '%' . $keywords . '%'], ['private', '=', false]])
                ->limit(4)->get();
            $results['playlists'] = $playlists;

            return $results;
        }
        switch ($type) {
            case 'track':
                $tracks = Track::whereHas('album.artist.user', function (Builder $query) use ($keywords) {
                    $query->where('name', 'ilike', '%' . $keywords . '%');
                })->orWhere(
                    'name',
                    'ilike',
                    '%' . $keywords . '%',
                )
                    ->paginate(10);
                // var_dump($tracks);
                return new TrackCollection($tracks);
                break;

            case 'album':
                $albums = Album::whereHas('artist.user', function (Builder $query) use ($keywords) {
                    $query->where('name', 'ilike', '%' . $keywords . '%');
                })->orWhere(
                    'name',
                    'ilike',
                    '%' . $keywords . '%',
                )
                    ->paginate(10);
                return new AlbumCollection($albums);
            case 'artist':
                $artists = Artist::whereHas('user', function (Builder $query) use ($keywords) {
                    $query->where('name', 'ilike', '%' . $keywords . '%');
                })->paginate(10);
                return new ArtistCollection($artists);
            case 'playlist':
                $playlists = Playlist::withCount('tracks')
                    ->where([['name', 'ilike', '%' . $keywords . '%'], ['private', '=', false]])
                    ->paginate(10);
                return new PlaylistCollection($playlists);


            default:
                return response()->json([], 404);

                break;
        }
    }
    // public function searchByType($keywords, $type, Request $request)
    // {
    //     return $keywords . $type;
    // }
}
