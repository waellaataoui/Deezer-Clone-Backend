<?php

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\PlaylistsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/user', function (Request $request) {
//     //still sometimes null no fkn idea why eventho login succeed
//     $user = auth()->user();
//     // var_dump($request);
//     if ($user) $user->isArtist = $user->isArtist();
//     return $user;
// })->middleware(['auth:sanctum']);
Route::post('/register', [UserController::class, 'store'])
    ->middleware(['guest:' . config('fortify.guard')]);


Route::get('/', function () {
    return view('welcome');
});
Route::get('/playlists', [PlaylistsController::class, 'getPlaylists']);


Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});
