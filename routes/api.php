<?php

use App\Models\HikeVtt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HikeVttController;
use App\Http\Controllers\ClubPostController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StripeController;
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

Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/forgot', [AuthController::class, 'forgot']);
Route::post('/verifyResetPassword', [AuthController::class, 'verifyResetPassword']);
Route::post('/resetPassword', [AuthController::class, 'resetPassword']);

// route::post('/checkout', [UserController::class, 'checkout']);
route::post('/create-payment-intent', [StripeController::class, 'paymentIntent']);


// Pour ADMIN Stripe
route::post('/payment/product', [PaymentController::class, 'product']);
route::post('/payment/product/{product_id}/price', [PaymentController::class, 'price']);


Route::middleware('auth:sanctum')->group(function () {
  Route::post('/me', [AuthController::class, 'me']);
  Route::post('/disconnect', [AuthController::class, 'disconnect']);

  // SUBSCRIPTIONS

  route::get('/subscriptions/plans', [PaymentController::class, 'plans']);
  route::post('/subscriptions/create', [PaymentController::class, 'create']);
  route::post('/subscriptions/cancel', [PaymentController::class, 'cancel']);
  route::get('/subscriptions/check', [PaymentController::class, 'check']);
  route::post('/subscriptions/deleteFails', [PaymentController::class, 'deleteFailsSubs']);
  route::post('/subscriptions/delete', [PaymentController::class, 'deleteSubscription']);
  route::post('/subscriptions/subscribe', [PaymentController::class, 'subscribe']);

  route::get('/invoices', [PaymentController::class, 'invoices']);

  // POSTS
  Route::get('/posts', [PostController::class, 'posts']);
  Route::get('/posts/{post}/{type}/show', [PostController::class, 'show']);


  // CLUBS
  Route::get('/clubs', [ClubController::class, 'clubs']);
  Route::post('/clubs', [ClubController::class, 'storeClub']);
  Route::delete('/deleteImagePost/{image_id}', [ClubController::class, 'deleteImagePost']);
  Route::put('/clubs/{club}', [ClubController::class, 'updateClub']);
  Route::get('/clubs/{club}/clubInformations', [ClubController::class, 'clubInformations']);
  Route::post('/clubs/{club}/followOrUnfollow', [ClubController::class, 'followOrUnfollow']);
  Route::post('/clubs/{club}/requestToJoin', [ClubController::class, 'requestToJoin']);

  Route::get('/clubs/{club}/profileImages', [ClubController::class, 'profileImages']);
  Route::get('/clubs/{club}/allImages', [ClubController::class, 'allImages']);

  Route::get('/clubs/{club}/posts', [ClubPostController::class, 'posts']);
  Route::put('/clubs/{club}/posts/{post}', [ClubPostController::class, 'updatePost']);
  Route::delete('/clubs/{club}/posts/{post}', [ClubPostController::class, 'deletePost']);
  Route::post('/clubs/{club}/posts/{post}/likeOrUnlike', [ClubPostController::class, 'likeOrUnlike']);
  Route::get('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'comments']);
  Route::post('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'storeComment']);
  Route::put('/comments/{comment}', [ClubPostController::class, 'updateComment']);
  Route::delete('/comments/{comment}', [ClubPostController::class, 'deleteComment']);


  // USERS
  Route::post('/users/posts', [UserPostController::class, 'storePost']);
  Route::put('/users/posts/{post}', [UserPostController::class, 'updatePost']);
  Route::delete('/users/posts/{post}', [UserPostController::class, 'deletePost']);
  Route::post('/users/posts/{post}/likeOrUnlike', [UserPostController::class, 'likeOrUnlike']);
  Route::get('/users/{user}/posts', [UserPostController::class, 'posts']);
  Route::post('/users/posts/{post}/comments', [UserPostController::class, 'storeComment']);
  Route::get('/users/posts/{post}/comments', [UserPostController::class, 'comments']);
  Route::put('/users/posts/comments/{comment}', [UserPostController::class, 'updateComment']);
  Route::delete('/users/posts/comments/{comment}', [UserPostController::class, 'deleteComment']);

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
  Route::get('/hikes/vtt/{hike_id}', [HikeVttController::class, 'show']);
  Route::put('/hikes/vtt/{hike_id}', [HikeVttController::class, 'update']);
  Route::delete('/hikes/vtt/{hike_id}', [HikeVttController::class, 'delete']);
  Route::post('/hikes/vtt/{hike_id}/hypeOrUnhype', [HikeVttController::class, 'hypeOrUnhype']);

  Route::middleware('admin.club')->group(function () {
    Route::post('/clubs/{club}/acceptRequestToJoin', [ClubController::class, 'acceptRequestToJoin']);
    Route::post('/clubs/{club}/denyRequestToJoin', [ClubController::class, 'denyRequestToJoin']);
    Route::get('/clubs/{club}/showJoinRequests', [ClubController::class, 'showJoinRequests']);
    Route::post('/clubs/{club}/changeAdmin', [ClubController::class, 'changeAdmin']);

    Route::post('/clubs/{club}/posts', [ClubPostController::class, 'storePost']);

    Route::post('/hikes/vtt', [HikeVttController::class, 'store']);

    Route::post('/hikes/vtt/{hike_id}/storeFlyer', [HikeVttController::class, 'storeFlyer']);
    Route::post('/hikes/vtt/{hike_id}/storeImage', [HikeVttController::class, 'storeImage']);

    Route::post('/hikes/vtt/{hike_id}/storeTrip', [HikeVttController::class, 'storeTrip']);
  });

  Route::middleware('admin.club.or.premium.one')->get('/clubs/{club}/clubMembers', [ClubController::class, 'clubMembers']);

  Route::middleware('premium')->group(function () {
    Route::post('/hikes/vtt/searchInDepartment', [HikeVttController::class, 'searchInDepartment']);
    Route::post('/hikes/vtt/searchInMonth', [HikeVttController::class, 'searchInMonth']);
  });
});
