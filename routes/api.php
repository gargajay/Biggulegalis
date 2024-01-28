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
        Route::get('get-commite-members', [UserController::class, 'getCommiteMembers']);
        Route::post('add-commite-members', [UserController::class, 'addCommiteMembers']);
        Route::post('delete-commite-members', [UserController::class, 'deleteCommiteMember']);
        
        Route::middleware(['CheckAssociation'])->group(function () {
            Route::post('save-permissions', [UserController::class, 'savePermission']);
            Route::post('get-profile', [UserController::class, 'getProfile']);
            Route::post('invitation_accept_reject', [LinkController::class, 'invitationAcceptReject']);
            Route::post('invitation-accept-reject', [LinkController::class, 'invitationAcceptReject']);
            Route::post('send-invitation', [HomeController::class, 'sendInvitation']);
            Route::get('buy-document', [HomeController::class, 'buyDocument']);
             /******************************-----announcement API-----************************************/


        Route::post('announcement', [HomeController::class, 'announcement']); // staff/
        Route::post('delete-announcement', [HomeController::class, 'deleteAnnouncement']);

        /******************************-----Quote API-----************************************/

        Route::get('get-all-document', [HomeController::class, 'getAllDocument']);



        Route::post('other-person', [HomeController::class, 'otherPerson']); // staff/
        Route::post('delete-other-person', [HomeController::class, 'deleteOtherPerson']);

        Route::post('quote', [HomeController::class, 'quote']); // staff/
        Route::post('delete-quote', [HomeController::class, 'deleteQuote']);


    /******************************-----compliant API-----************************************/

    Route::post('document-list', [HomeController::class, 'getAllDocument']);

         Route::post('compliant', [HomeController::class, 'compliant']); // staff/
         Route::post('delete-compliant', [HomeController::class, 'deletecompliant']);

        });


        

        Route::post('update-profile', [UserController::class, 'updateProfile']);

        Route::post('edit-address', [UserController::class, 'editAddress']);
        Route::get('s3-token', [UserController::class, 'generateS3SecurityToken']);
        // update subscription
        // Route::post('update-susbcription', [SubscriptionController::class, 'updateSubscription']);
        // Route::get('susbcription-detail', [SubscriptionController::class, 'subscriptionDetail']);

        /******************************-----NOTIFICATION API-----************************************/
        Route::post('get-notification', [NotificationController::class, 'getNotification']);
        Route::post('read-notification', [NotificationController::class, 'readNotification']);


       

        /******************************-----Link API-----************************************/
   
        Route::post('link', [LinkController::class, 'link']); // add/update link
        Route::post('delete-link', [LinkController::class, 'deleteLink']);
        Route::post('get-invitation', [LinkController::class, 'getInviationList']);

        

       

       

        /******************************-----Galley API-----************************************/
       
        Route::post('gallery', [GalleryController::class, 'gallery']); // add/update link
        Route::post('delete-gallery', [GalleryController::class, 'deleteGallery']);

        /******************************-----Staff API-----************************************/


        Route::post('add-staff', [UserController::class, 'staff']); // staff/
        Route::post('delete-staff', [UserController::class, 'deleteStaff']);

        Route::post('add-old-member', [UserController::class, 'oldMember']); // staff/
        Route::post('delete-old-member', [UserController::class, 'deleteOldMember']);


        Route::post('remove-from-association', [UserController::class, 'removeFromAssociation']);


        


    


       






       
    });
});


// Run if not route found
Route::any('{any}', function () {
    PublicException::Error('PAGE_NOT_FOUND', STATUS_NOT_FOUND);
})->where('any', '.*');
