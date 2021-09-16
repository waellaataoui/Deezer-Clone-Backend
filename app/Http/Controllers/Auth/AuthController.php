<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Artist;
use App\Models\Playlist;
use App\Models\NormalUser;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use App\Http\Resources\TrackCollection;
use App\Http\Resources\PlaylistResource;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'confirmed'],
        ])->validate();
        $user = new User();
        DB::transaction(function () use ($user, $request) {
            $normalUser = new NormalUser;
            $favouriteTracks = new Playlist;
            $normalUser->save();
            $favouriteTracks->fill(['name' => 'Favourite Tracks']);
            $favouriteTracks->owner()->associate($normalUser);
            $normalUser->favouriteTracks()->save($favouriteTracks);
            $user->fill([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);
            $normalUser->user()->save($user);
        });

        return response()->json([
            'user' => $user
        ], 201);
    }

    public function registerArtist(Request $request)
    {

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => ['required', 'string', 'confirmed'],
        ])->validate();
        $artist = Artist::create();

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        $artist->user()->save($user);
        return response()->json([
            'artist' => $user
        ], 201);
    }
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response([
                'message' => 'Invalid credentials!'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $user->isArtist = $user->isArtist();
        if (!$user->isArtist) {
            $favouriteTracks = $user->type->favouriteTracks;
            $user->favouriteTracks = [
                'id' => $favouriteTracks->id,
                'tracks' => new TrackCollection($favouriteTracks->tracks)
            ];
        }
        $token = $user->createToken('token')->plainTextToken;

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day

        return response([
            'user' => $user
        ])->withCookie($cookie);
    }

    public function user()
    {

        $user = Auth::user();
        $user->isArtist = $user->isArtist();
        if (!$user->isArtist) {
            $favouriteTracks = $user->type->favouriteTracks;
            $user->favouriteTracks = [
                'id' => $favouriteTracks->id,
                'tracks' => new TrackCollection($favouriteTracks->tracks)
            ];
        }
        return $user;
    }

    public function logout(Request $request)
    {
        //the token value(encryption) is not the same when using socials
        //thus deleting fails
        $token =  PersonalAccessToken::findToken($request->bearerToken());
        if ($token) $token->delete();
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'Success',
        ])->withCookie($cookie);
    }
}
