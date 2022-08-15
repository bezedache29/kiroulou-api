<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;

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

  Route::post('/clubs/membershipRequest', [ClubController::class, 'membershipRequest']);
  Route::post('/clubs/acceptMembershipRequest', [ClubController::class, 'acceptMembershipRequest']);
});
