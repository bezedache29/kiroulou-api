<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClubPostController;
use App\Http\Controllers\UserPostController;

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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
  Route::post('/me', [AuthController::class, 'me']);
  Route::post('/disconnect', [AuthController::class, 'disconnect']);

  Route::get('/clubs', [ClubController::class, 'index']);
  Route::post('/clubs', [ClubController::class, 'store']);
  Route::post('/clubs/{club}/followOrUnfollow', [ClubController::class, 'followOrUnfollow']);
  Route::post('/clubs/{club}/requestToJoin', [ClubController::class, 'requestToJoin']);
  Route::post('/clubs/{club}/acceptRequestToJoin', [ClubController::class, 'acceptRequestToJoin']);
  Route::post('/clubs/{club}/denyRequestToJoin', [ClubController::class, 'denyRequestToJoin']);
  Route::get('/clubs/{club}/showJoinRequests', [ClubController::class, 'showJoinRequests']);

  Route::get('/clubs/{club}/posts', [ClubPostController::class, 'posts']);
  Route::post('/clubs/{club}/posts', [ClubPostController::class, 'storePost']);
  Route::get('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'comments']);
  Route::post('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'storeComment']);

  Route::post('/posts', [UserPostController::class, 'storePost']);
  Route::post('/posts/{post}/likeOrUnlike', [UserPostController::class, 'likeOrUnlike']);

  Route::get('/users/{user}/posts', [UserPostController::class, 'posts']);
  Route::post('/posts/{post}/comments', [UserPostController::class, 'storeComment']);
  Route::get('/posts/{post}/comments', [UserPostController::class, 'comments']);

  Route::get('/users/{user}/bikes', [UserController::class, 'bikes']);
  Route::post('/bikes', [UserController::class, 'storeBike']);

  Route::get('/users/{user}/profileImages', [UserController::class, 'profileImages']);
  Route::get('/users/{user}/allImages', [UserController::class, 'allImages']);
});
