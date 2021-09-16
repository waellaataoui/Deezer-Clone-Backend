<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Playlist;
use App\Models\NormalUser;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;


class SocialLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware(['social', 'web']);
    }

    public function redirect($service)
    {
        return Socialite::driver($service)->stateless()->redirect();
    }

    public function callback($service)
    {

        try {
            $user = Socialite::driver($service)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/login-error');
        }
        $email = $user->getEmail();
        $existingUser = User::where('email', $email)->first();
        if (!$existingUser) {
            $newUser = new User;
            DB::transaction(function () use ($user, $newUser, $email) {
                $normalUser = NormalUser::create();
                $favouriteTracks = new Playlist;

                $newUser->fill([
                    'name' => $user->getName(),
                    'email' => $email,
                    'avatar' => $user->getAvatar(),
                    'password' => ''
                ]);
                $normalUser->user()->save($newUser);
                $favouriteTracks->fill(['name' => 'Favourite Tracks']);
                $favouriteTracks->owner()->associate($normalUser);
                $normalUser->favouriteTracks()->save($favouriteTracks);
                $normalUser->user()->save($newUser);
            });
            $existingUser = $newUser;
        }
        try {
            Auth::login($existingUser, true);
            $token = $existingUser->createToken('token')->plainTextToken;
            $cookie = cookie('jwt', $token, 60 * 24); // 1 day
            return redirect(env('CLIENT_BASE_URL'))->withCookie($cookie);
        } catch (\Throwable $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/login-errror');
        }
    }
}
