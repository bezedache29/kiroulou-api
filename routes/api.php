<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\HikeVttController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ClubPostController;
use App\Http\Controllers\KiroulouController;
use App\Http\Controllers\PasswordController;
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

  route::get('/billing', [PaymentController::class, 'billing']);

  // POSTS
  Route::get('/posts', [PostController::class, 'posts']);
  Route::get('/posts/{post}/{type}/show', [PostController::class, 'show']);


  // CLUBS
  Route::get('/clubs', [ClubController::class, 'clubs']);
  Route::post('/clubs', [ClubController::class, 'storeClub']);
  Route::delete('/clubs/{club}', [ClubController::class, 'deleteClub']);
  Route::post('/clubs/{club}/storeAvatar', [ClubController::class, 'storeAvatar']);
  Route::get('/clubs/organizations', [ClubController::class, 'organizations']);
  Route::delete('/deleteImagePost/{image_id}', [ClubController::class, 'deleteImagePost']);
  Route::put('/clubs/{club}', [ClubController::class, 'updateClub']);
  Route::get('/clubs/{club}/clubInformations', [ClubController::class, 'clubInformations']);
  Route::post('/clubs/{club}/followOrUnfollow', [ClubController::class, 'followOrUnfollow']);
  Route::get('/clubs/{club}/isClubFollowed', [ClubController::class, 'isClubFollowed']);
  Route::post('/clubs/{club}/requestToJoin', [ClubController::class, 'requestToJoin']);

  Route::get('/clubs/{club}/profileImages', [ClubController::class, 'profileImages']);
  Route::get('/clubs/{club}/allImages', [ClubController::class, 'allImages']);

  Route::get('/clubs/{club}/posts', [ClubPostController::class, 'posts']);
  Route::put('/clubs/{club}/posts/{post}', [ClubPostController::class, 'updatePost']);
  Route::delete('/clubs/{club}/posts/{post}', [ClubPostController::class, 'deletePost']);
  Route::post('/clubs/{club}/posts/{post}/likeOrUnlike', [ClubPostController::class, 'likeOrUnlike']);
  Route::get('/clubs/{club}/posts/{post}/isPostLiked', [ClubPostController::class, 'isPostLiked']);
  Route::get('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'comments']);
  Route::post('/clubs/{club}/posts/{post}/comments', [ClubPostController::class, 'storeComment']);
  Route::put('/comments/{comment}', [ClubPostController::class, 'updateComment']);
  Route::delete('/comments/{comment}', [ClubPostController::class, 'deleteComment']);


  // USERS
  Route::post('/users/posts', [UserPostController::class, 'storePost']);
  Route::post('/users/posts/{post}/storeImage', [UserPostController::class, 'storeImage']);
  Route::put('/users/posts/{post}', [UserPostController::class, 'updatePost']);
  Route::delete('/users/posts/deleteImage', [UserPostController::class, 'deleteImage']);
  Route::delete('/users/posts/{post}', [UserPostController::class, 'deletePost']);
  Route::post('/users/posts/{post}/likeOrUnlike', [UserPostController::class, 'likeOrUnlike']);
  Route::get('/users/posts/{post}/isPostLiked', [UserPostController::class, 'isPostLiked']);
  Route::get('/users/{user}/posts', [UserPostController::class, 'posts']);
  Route::post('/users/posts/{post}/comments', [UserPostController::class, 'storeComment']);
  Route::get('/users/posts/{post}/comments', [UserPostController::class, 'comments']);
  Route::put('/users/posts/comments/{comment}', [UserPostController::class, 'updateComment']);
  Route::delete('/users/posts/comments/{comment}', [UserPostController::class, 'deleteComment']);

  Route::get('/users/{user}', [UserController::class, 'user']);
  Route::put('/users/{user}', [UserController::class, 'userUpdate']);
  Route::put('/users/{user}/admin', [UserController::class, 'admin']);
  Route::delete('/users/{user}', [UserController::class, 'userDelete']);
  Route::post('/users/{user}/storeAvatar', [UserController::class, 'storeAvatar']);

  Route::put('/users/{user}/leaveClub', [UserController::class, 'leaveClub']);

  Route::get('/users/{user}/bikes', [UserController::class, 'bikes']);
  Route::post('/users/bikes', [UserController::class, 'storeBike']);
  Route::get('/users/bikes/types', [UserController::class, 'bikeTypes']);
  Route::post('/users/bikes/{bike}/storeImageBike', [UserController::class, 'storeImageBike']);
  Route::put('/users/bikes/{bike}', [UserController::class, 'updateBike']);
  Route::delete('/users/bikes/{bike}', [UserController::class, 'deleteBike']);

  Route::get('/users/{user}/profileImages', [UserController::class, 'profileImages']);
  Route::get('/users/{user}/allImages', [UserController::class, 'allImages']);
  Route::get('/users/{user}/allImagesCount', [UserController::class, 'allImagesCount']);

  Route::get('/users/{user}/followedClubs', [UserController::class, 'followedClubs']);
  Route::get('/users/{user}/followedUsers', [UserController::class, 'followedUsers']);
  Route::get('/users/{user}/isUserFollowed', [UserController::class, 'isUserFollowed']);
  Route::post('/users/{user}/followOrUnfollow', [UserController::class, 'followOrUnfollow']);


  // HIKES VTT
  Route::middleware('premium')->group(function () {
    Route::post('/hikes/vtt/searchInDepartment', [HikeVttController::class, 'searchInDepartment']);
    Route::post('/hikes/vtt/searchInMonth', [HikeVttController::class, 'searchInMonth']);
  });


  Route::get('/hikes/vtt', [HikeVttController::class, 'index']);
  Route::get('/hikes/vtt/{hike_id}', [HikeVttController::class, 'show']);
  Route::post('/hikes/vtt/{hike_id}/hypeOrUnhype', [HikeVttController::class, 'hypeOrUnhype']);
  Route::post('/hikes/vtt/search', [HikeVttController::class, 'searchHikes']);


  // ADDRESSES
  Route::post('/addresses/isAlreadyExist', [KiroulouController::class, 'isAlreadyExist']);
  Route::post('/addresses/create', [KiroulouController::class, 'createAddress']);


  Route::middleware('admin.club')->group(function () {
    Route::post('/clubs/{club}/acceptRequestToJoin', [ClubController::class, 'acceptRequestToJoin']);
    Route::delete('/clubs/{club}/denyRequestToJoin', [ClubController::class, 'denyRequestToJoin']);
    Route::get('/clubs/{club}/showJoinRequests', [ClubController::class, 'showJoinRequests']);
    Route::post('/clubs/{club}/changeAdmin', [ClubController::class, 'changeAdmin']);
    Route::delete('/clubs/{club}', [ClubController::class, 'deleteClub']);

    Route::post('/clubs/{club}/posts', [ClubPostController::class, 'storePost']);

    Route::post('/hikes/vtt', [HikeVttController::class, 'store']);
    Route::put('/hikes/vtt/{hike_id}', [HikeVttController::class, 'update']);
    Route::delete('/hikes/vtt/{hike_id}', [HikeVttController::class, 'delete']);

    Route::put('/hikes/vtt/{hike_id}/changeDate', [HikeVttController::class, 'changeDate']);

    Route::post('/hikes/vtt/{hike_id}/storeFlyer', [HikeVttController::class, 'storeFlyer']);
    Route::post('/hikes/vtt/{hike_id}/storeImage', [HikeVttController::class, 'storeImage']);
    Route::delete('/hikes/vtt/{hike_id}/deleteImages', [HikeVttController::class, 'deleteImages']);

    Route::post('/hikes/vtt/{hike_id}/trip', [HikeVttController::class, 'storeTrip']);
    Route::put('/hikes/vtt/{hike_id}/trip/{trip_id}', [HikeVttController::class, 'updateTrip']);
    Route::delete('/hikes/vtt/{hike_id}/trip/{trip_id}', [HikeVttController::class, 'deleteTrip']);
  });

  // middleware admin.club.or.premium.one
  Route::get('/clubs/{club}/clubMembers', [ClubController::class, 'clubMembers']);
  // Route::middleware('admin.club.or.premium.one')->get('/clubs/{club}/clubMembers', [ClubController::class, 'clubMembers']);

  // middleware admin.club
  Route::put('/clubs/{club}/users/{user}/expelMember', [ClubController::class, 'expelMember']);
});
