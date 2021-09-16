<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\NormalUser;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function getUserById($id)
    {
        $user = NormalUser::find($id);
        if ($user) {
            return  $user->user;
        } else return response()->json([], 404);
    }



    // public function store(Request $request)
    // {
    //     $normalUser = NormalUser::create();
    //     $creator = new CreateNewUser();
    //     $user = $creator->create($request->all());
    //     $normalUser->user()->save($user);

    //     // $user->type()->save($normalUser);
    //     return response()->json([
    //         'user' => $user
    //     ], 201);
    // }
}
