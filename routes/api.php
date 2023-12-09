<?php

use App\Exceptions\PublicException;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CronController;
use App\Http\Controllers\Api\LinkController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\GalleryController;
use Illuminate\Support\Facades\Route;

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


Route::group([], function () {
    //Route without auth

    /******************************-----AUTH API-----************************************/
    Route::post('signup', [AuthController::class, 'signup'])->middleware(['SkipLogAfterRequest']);
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware(['SkipLogAfterRequest']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('send-otp', [AuthController::class, 'sendOTP']);
    Route::post('verify-otp', [AuthController::class, 'verifyOTP']);
    Route::post('reset-password', [AuthController::class, 'resetPasswordUsingOTP']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('app-settings', [AuthController::class, 'appSettings']);
    Route::get('get-goal', [AuthController::class, 'getGoal']);
    Route::get('get-all-document', [HomeController::class, 'getAllDocument']);

    
    Route::get('checkCron', [CronController::class, 'sendLinkReminder']);
    Route::get('get-countries', [HomeController::class, 'getCountries']);
    Route::post('get-state', [HomeController::class, 'getState']);
    Route::post('get-district', [HomeController::class, 'getDistrict']);
    Route::post('get-tehsil', [HomeController::class, 'getTehsil']);
    Route::post('get-link', [LinkController::class, 'getLink']);
    Route::post('get-link-details', [LinkController::class, 'getLinkDetails']);
    Route::post('get-association-detail', [HomeController::class, 'getAssociationDetail']);

    Route::post('get-gallery', [GalleryController::class, 'getGallery']);
    Route::post('get-gallery-details', [GalleryController::class, 'getGalleryDetails']);
    Route::post('member-list', [HomeController::class, 'getAllMembers']);
    Route::post('document-list', [HomeController::class, 'getAllDocument']);



    Route::post('filter-members', [HomeController::class, 'filterMembers']);

    // Route::post('liap/google-notifications', [SubscriptionController::class, 'googleNotifications']);
    // Route::post('liap/apple-notifications', [SubscriptionController::class, 'appleNotifications']);


    Route::middleware(['auth:api', 'UserLocalization'])->group(function () {

        //Route with auth

        /******************************-----AUTH API-----************************************/
        Route::post('change-password', [AuthController::class, 'changePassword'])->middleware(['SkipLogAfterRequest']);
        Route::post('notifications-on-off', [AuthController::class, 'notificationsOnOff']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('delete-account', [AuthController::class, 'deleteAccount']);

        /******************************-----HOME SCREEN API-----************************************/
        Route::get('home', [HomeController::class, 'home']);

        /******************************-----USER API-----************************************/
        
        Route::get('get-group-role-list', [UserController::class, 'getGroupRoleList']);

        Route::post('update-profile', [UserController::class, 'updateProfile']);
        Route::post('save-permissions', [UserController::class, 'savePermission']);
        Route::post('edit-address', [UserController::class, 'editAddress']);
        Route::post('get-profile', [UserController::class, 'getProfile']);
        Route::get('s3-token', [UserController::class, 'generateS3SecurityToken']);
        // update subscription
        // Route::post('update-susbcription', [SubscriptionController::class, 'updateSubscription']);
        // Route::get('susbcription-detail', [SubscriptionController::class, 'subscriptionDetail']);

        /******************************-----NOTIFICATION API-----************************************/
        Route::post('get-notification', [NotificationController::class, 'getNotification']);
        Route::post('read-notification', [NotificationController::class, 'readNotification']);


        /******************************----- ADDING POSTS API-----************************************/
        Route::get('get-post', [PostController::class, 'getPost']);
        Route::post('add-update-post', [PostController::class, 'addUpdatePost']);
        Route::post('delete-post', [PostController::class, 'deletePost']);
        Route::post('delete-post-image', [PostController::class, 'deletePostImage']);
        Route::post('like-dislike-post', [PostController::class, 'likeDislikePost']);
        Route::post('post-comment', [PostController::class, 'postComment']);
        Route::post('delete-post-comment', [PostController::class, 'deletePostComment']);
        Route::post('get-post-detail', [PostController::class, 'getpostDetail']);
        Route::post('get-comment', [PostController::class, 'getComment']);

        /******************************-----Link API-----************************************/
   
        Route::post('link', [LinkController::class, 'link']); // add/update link
        Route::post('delete-link', [LinkController::class, 'deleteLink']);
        Route::post('get-invitation', [LinkController::class, 'getInviationList']);
        Route::post('invitation-accept-reject', [LinkController::class, 'invitationAcceptReject']);
        Route::post('send-invitation', [HomeController::class, 'sendInvitation']);

       

       

        /******************************-----Galley API-----************************************/
       
        Route::post('gallery', [GalleryController::class, 'gallery']); // add/update link
        Route::post('delete-gallery', [GalleryController::class, 'deleteGallery']);

        /******************************-----Staff API-----************************************/


        Route::post('add-staff', [UserController::class, 'staff']); // staff/
        Route::post('delete-staff', [UserController::class, 'deleteStaff']);

        Route::post('add-old-member', [UserController::class, 'oldMember']); // staff/
        Route::post('delete-old-member', [UserController::class, 'deleteOldMember']);


        Route::post('remove-from-association', [UserController::class, 'removeFromAssociation']);


        


    


        /******************************-----announcement API-----************************************/


        Route::post('announcement', [HomeController::class, 'announcement']); // staff/
        Route::post('delete-announcement', [HomeController::class, 'deleteAnnouncement']);

        /******************************-----Quote API-----************************************/


        Route::post('quote', [HomeController::class, 'quote']); // staff/
        Route::post('delete-quote', [HomeController::class, 'deleteQuote']);







        /******************************-----CHAT API-----************************************/
        Route::post('chat-group', [GroupController::class, 'chatGroup']);
        Route::post('add-members-to-group', [GroupController::class, 'addMembersToChatGroup']);
        Route::post('remove-member-from-group', [GroupController::class, 'removeMemberFromChatGroup']);
        Route::post('make-member-group-admin', [GroupController::class, 'makeMemberGroupAdmin']);
        Route::post('dismiss-member-as-admin', [GroupController::class, 'dismissMemberasAdmin']);
        Route::post('delete-group', [GroupController::class, 'deleteGroup']);
        Route::get('chat-list', [ChatController::class, 'chatList']);
        Route::post('chat-details', [ChatController::class, 'chatDetail']);
        Route::post('block-unblock-user', [ChatController::class, 'blockUnblockUser']);
        Route::get('get-blocked-user', [ChatController::class, 'getBlockedUser']);
        Route::post('send-message', [ChatController::class, 'saveMessage']);
    });
});


// Run if not route found
Route::any('{any}', function () {
    PublicException::Error('PAGE_NOT_FOUND', STATUS_NOT_FOUND);
})->where('any', '.*');
