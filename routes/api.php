<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PlaylistsController;
use App\Http\Controllers\FavouritesController;
use App\Http\Controllers\Auth\SocialLoginController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('/artist/register', [AuthController::class, 'registerArtist']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('/login/{service}', [SocialLoginController::class, 'redirect']);
    Route::get('/login/{service}/callback', [SocialLoginController::class, 'callback']);
    Route::post('logout', [AuthController::class, 'logout']);
});
Route::group([], function () {
    Route::get('/albums/latest', [AlbumController::class, 'newReleases']);
    Route::get('/albums/{id}', [AlbumController::class, 'getById']);
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::get('/artists/{id}/albums', [ArtistController::class, 'getAlbums']);
    Route::get('/artists/{id}', [ArtistController::class, 'getArtistById']);

    //favourites route
    Route::group(['prefix' => '/favourites'], function () {
        Route::get('/{id}/{type}', [FavouritesController::class, 'getByType']);
        Route::post('/loved', [FavouritesController::class, 'addToFavouriteTracks'])->middleware(['auth:sanctum']);
        Route::delete('/loved', [FavouritesController::class, 'removeFromFavouriteTracks'])->middleware(['auth:sanctum']);
    });

    Route::group(['prefix' => '/playlists'], function () {
        Route::get('/', [PlaylistsController::class, 'getPlaylists']);
        Route::get('/{id}', [PlaylistsController::class, 'getById'])->where('id', '[0-9]+');
    });

    //search routes
    Route::group(['prefix' => '/search'], function () {
        Route::get('/autocomplete', [SearchController::class, 'autocomplete']);
        Route::get('/{keywords}/{type?}', [SearchController::class, 'search']);
        // Route::get('/{keywords}/{type}', [SearchController::class, 'searchByType']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [AuthController::class, 'user']);

    Route::group(['prefix' => '/playlists', 'middleware' => ['isUser']], function () {
        Route::post('/', [PlaylistsController::class, 'createPlaylist']);
        Route::post('/addTracks', [PlaylistsController::class, 'addTracks']);
        Route::post('/removeTracks', [PlaylistsController::class, 'removeTracks']);
        Route::get('/mine', [PlaylistsController::class, 'myPlaylists']);
        // Route::get('/{id}', [PlaylistsController::class, 'getById']);
    });
});
//album route
Route::group(['prefix' => '/artist', 'middleware' => ['auth:sanctum', 'isArtist']], function () {
    Route::group(['prefix' => '/album'], function () {
        Route::post('/', [AlbumController::class, 'create']);

        Route::post('/single', [AlbumController::class, 'single']);
    });
});
