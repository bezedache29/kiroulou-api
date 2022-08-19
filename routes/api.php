<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClubPostController;
use App\Http\Controllers\HikeVttController;
use App\Http\Controllers\UserPostController;
use App\Models\HikeVtt;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/login', [AuthController::class, 'unauthenticated'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/me', [AuthController::class, 'me']);
  Route::post('/disconnect', [AuthController::class, 'disconnect']);


  // POSTS
  Route::get('/posts', [PostController::class, 'posts']);
  Route::get('/posts/{post}/{type}/show', [PostController::class, 'show']);


  // CLUBS
  Route::get('/clubs', [ClubController::class, 'clubs']);
  Route::post('/clubs', [ClubController::class, 'storeClub']);
  Route::get('/clubs/{club}/clubInformations', [ClubController::class, 'clubInformations']);
  Route::post('/clubs/{club}/followOrUnfollow', [ClubController::class, 'followOrUnfollow']);
  Route::post('/clubs/{club}/requestToJoin', [ClubController::class, 'requestToJoin']);

  Route::get('/clubs/{club}/profileImages', [ClubController::class, 'profileImages']);
  Route::get('/clubs/{club}/allImages', [ClubController::class, 'allImages']);

  Route::get('/clubs/{club}/posts', [ClubPostController::class, 'posts']);
  Route::post('/clubs/{club}/posts/{post}/likeOrUnlike', [ClubPostController::class, 'likeOrUnlike']);
  Route::get('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'comments']);
  Route::post('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'storeComment']);


  // USERS
  Route::post('/users/posts', [UserPostController::class, 'storePost']);
  Route::post('/users/posts/{post}/likeOrUnlike', [UserPostController::class, 'likeOrUnlike']);
  Route::get('/users/{user}/posts', [UserPostController::class, 'posts']);
  Route::post('/users/posts/{post}/comments', [UserPostController::class, 'storeComment']);
  Route::get('/users/posts/{post}/comments', [UserPostController::class, 'comments']);

  Route::get('/users/{user}/bikes', [UserController::class, 'bikes']);
  Route::post('/users/bikes', [UserController::class, 'storeBike']);
  Route::put('/users/bikes/{bike}', [UserController::class, 'updateBike']);
  Route::delete('/users/bikes/{bike}', [UserController::class, 'deleteBike']);

  Route::get('/users/{user}/profileImages', [UserController::class, 'profileImages']);
  Route::get('/users/{user}/allImages', [UserController::class, 'allImages']);

  Route::get('/users/{user}/followedClubs', [UserController::class, 'followedClubs']);
  Route::get('/users/{user}/followedUsers', [UserController::class, 'followedUsers']);
  Route::post('/users/{user}/followOrUnfollow', [UserController::class, 'followOrUnfollow']);


  // HIKES VTT
  Route::get('/hikes/vtt', [HikeVttController::class, 'index']);
  Route::get('/hikes/vtt/{hike_id}/show', [HikeVttController::class, 'show']);
  Route::post('/hikes/vtt/{hike_id}/hypeOrUnhype', [HikeVttController::class, 'hypeOrUnhype']);


  Route::middleware('admin.club')->group(function () {
    Route::post('/clubs/{club}/acceptRequestToJoin', [ClubController::class, 'acceptRequestToJoin']);
    Route::post('/clubs/{club}/denyRequestToJoin', [ClubController::class, 'denyRequestToJoin']);
    Route::get('/clubs/{club}/showJoinRequests', [ClubController::class, 'showJoinRequests']);
    Route::post('/clubs/{club}/changeAdmin', [ClubController::class, 'changeAdmin']);

    Route::post('/clubs/{club}/posts', [ClubPostController::class, 'storePost']);

    Route::post('/hikes/vtt', [HikeVttController::class, 'store']);
  });

  Route::middleware('admin.club.or.premium.one')->get('/clubs/{club}/clubMembers', [ClubController::class, 'clubMembers']);

  Route::middleware('premium')->group(function () {
    Route::post('/hikes/vtt/searchInDepartment', [HikeVttController::class, 'searchInDepartment']);
    Route::post('/hikes/vtt/searchInMonth', [HikeVttController::class, 'searchInMonth']);
  });
});
