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
  Route::post('/clubs/follow', [ClubController::class, 'follow']);
});
