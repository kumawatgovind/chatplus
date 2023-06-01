<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UsersController;
use App\Http\Controllers\Api\V1\CommonController;
use App\Http\Controllers\Api\V1\PostController;

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

Route::get('users/check', [UsersController::class, 'userCheck'])->name('user.check');

Route::group(['namespace' => 'Api\V1', 'as' => 'api.', 'middleware' => ['api.header', 'cors']], function () {

  Route::post('/check-phone-number', [UsersController::class, 'checkPhoneNumber']);
  Route::post('/check-username', [UsersController::class, 'checkUsername']);
  Route::post('/register', [UsersController::class, 'register']);
  Route::post('/upload-document', [UsersController::class, 'uploadDocument']);
  Route::post('/create-service-profile', [UsersController::class, 'createServiceProfile']);
  Route::get('/category-list/{categoryId?}', [CommonController::class, 'categoryList']);
  
Route::group(['middleware' => ['api.header', 'api.authtoken']], function () {
      
    Route::post('/users/update-kyc-document', [UsersController::class, 'updateKycDocument']);
    Route::post('/get-profile', [UsersController::class, 'getProfile']);
    Route::post('/users/edit-profile', [UsersController::class, 'updateProfile']);

    Route::post('/users/follow', [UsersController::class,'follwUserRequest']);
    Route::get('/users/following-follower', [UsersController::class,'getFollowerFollowing']);

    // Post Route
    Route::post('/create-post', [PostController::class,'createPost']);
    Route::post('/repost', [PostController::class,'rePost']);
    Route::post('/view-post', [PostController::class,'viewPostUpdate']);
    Route::post('/like-post', [PostController::class,'likePostUpdate']);
    Route::post('/get-post', [PostController::class,'getPost']);


    Route::get('/tag-list', [PostController::class,'tagsList']);

    Route::post('/users/timeline', [PostController::class,'getTimeline']);
    Route::post('/users/user-data', [PostController::class,'getUserData']);
    Route::post('/users/hashtag-timeline', [PostController::class,'getHashTagTimeline']);
    Route::post('/post-comment', [PostController::class,'postComment']);

    // Route::post('/users/change-password', [UsersController::class, 'changePassword'])->name('user.changePassword');
    // Route::post('/users/update-profile', [UsersController::class, 'updateProfile'])->name('user.updateProfile');
    // Route::get('/users/home-list', [UsersController::class, 'homeList'])->name('user.homeList');
    // Route::get('/users/category-list', [UsersController::class, 'categoryList'])->name('user.categoryList');
    // Route::post('/users/contact-us', [UsersController::class, 'contactUs'])->name('user.contactUs');
    // Route::post('/users/notification-list', [UsersController::class, 'notificationList'])->name('user.notificationList');
    // Route::post('/users/internal-messaging-list', [UsersController::class, 'internalMessagingList'])->name('user.internalMessagingList');
    // Route::post('/users/message-conversation-list', [UsersController::class, 'messageConversationList'])->name('user.messageConversationList');
    // Route::post('/users/message-send-text', [UsersController::class, 'sendInternalMessagingText'])->name('user.sendInternalMessagingText');
    // Route::post('/users/message-send-file', [UsersController::class, 'sendInternalMessagingFile'])->name('user.sendInternalMessagingFile');
    // Route::get('/users/logout', [UsersController::class, 'logout'])->name('user.logout');
    

  });
});
