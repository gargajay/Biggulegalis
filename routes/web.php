<?php

use App\Http\Controllers\Web\RoofController;
use App\Http\Controllers\Web\RoofPropertiesController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DocumentController;
use App\Http\Controllers\Web\MeasurementController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\SubscriptionPlanController;
use App\Http\Controllers\Web\TranslateController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\WorkoutHoursController;
use App\Http\Controllers\Web\AssociationController;
use App\Http\Controllers\Web\RazorpayController;
use App\Models\Association;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    //Route without auth

    /******************************-----AUTH ROUTE-----************************************/
    Route::get('/', [AuthController::class, 'login'])->name('home');
    Route::post('login', [AuthController::class, 'login'])->name('login')->middleware(['SkipLogAfterRequest']);
    Route::match(['get', 'post'], 'forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::match(['get', 'post'], 'reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset-password')->middleware(['SkipLogAfterRequest']);

    Route::get('payment',[RazorpayController::class,'payment']);
    Route::post('razorpay-payment',[RazorpayController::class,'store'])->name('razorpay.payment.store');



    // routes/web.php

Route::get('privacy-policy', [DashboardController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('about-us', [DashboardController::class, 'about'])->name('about-us');
Route::get('how-do-delete-account', [DashboardController::class, 'howDoDeleteAccount'])->name('how-do-delete-account');
Route::get('contact', [DashboardController::class, 'contactUs']);
Route::post('contact', [DashboardController::class, 'submit']);



  
    Route::middleware(['auth'])->group(function () {
        //Route with auth

        /******************************-----AUTH ROUTE-----************************************/
        Route::match(['get', 'post'], 'profile-update', [AuthController::class, 'updateProfile'])->name('profile-update');
        Route::match(['get', 'post'], 'change-password', [AuthController::class, 'changePassword'])->name('change-password')->middleware(['SkipLogAfterRequest']);
        Route::get('logout', [AuthController::class, 'logout'])->name('logout');


        /******************************-----DASHBOARD ROUTE-----************************************/
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        /******************************-----USER ROUTE-----************************************/
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::post('user-data', [UserController::class, 'getData'])->name('user.data');
        Route::get('user-detail/{id}', [UserController::class, 'viewDetails'])->where('id', '[0-9]+')->name('user.detail');
        Route::get('user-status/{id}', [UserController::class, 'changeStatus'])->where('id', '[0-9]+')->name('user.status');
        Route::get('user-delete/{id}', [UserController::class, 'deleteRow'])->where('id', '[0-9]+')->name('user.delete');

        /******************************-----Association ROUTE-----************************************/
        Route::get('association', [AssociationController::class, 'index'])->name('association');
        Route::post('association-data', [AssociationController::class, 'getData'])->name('association.data');
        Route::get('association-form/{id?}', [AssociationController::class, 'form'])->where('id', '[0-9]+')->name('association.form');
        Route::post('association-form-save/{id?}', [AssociationController::class, 'formSave'])->where('id', '[0-9]+')->name('association.form.save');
        Route::get('association-status/{id}', [AssociationController::class, 'changeStatus'])->where('id', '[0-9]+')->name('association.status');
        Route::get('association-delete/{id}', [AssociationController::class, 'deleteRow'])->where('id', '[0-9]+')->name('association.delete');


         /******************************-----Document ROUTE-----************************************/
         Route::get('document', [DocumentController::class, 'index'])->name('document');
         Route::post('document-data', [DocumentController::class, 'getData'])->name('document.data');
         Route::get('document-form/{id?}', [DocumentController::class, 'form'])->where('id', '[0-9]+')->name('document.form');
         Route::post('document-form-save/{id?}', [DocumentController::class, 'formSave'])->where('id', '[0-9]+')->name('document.form.save');
         Route::get('document-status/{id}', [DocumentController::class, 'changeStatus'])->where('id', '[0-9]+')->name('document.status');
         Route::get('document-delete/{id}', [DocumentController::class, 'deleteRow'])->where('id', '[0-9]+')->name('document.delete');


         /******************************-----WOKOUT HOURS ROUTE-----************************************/
         Route::get('workout-hours', [WorkoutHoursController::class, 'index'])->name('workout-hours');
         Route::post('workout-hours-data', [WorkoutHoursController::class, 'getData'])->name('workout-hours.data');
         Route::get('workout-hours-form/{id?}', [WorkoutHoursController::class, 'form'])->where('id', '[0-9]+')->name('workout-hours.form');
         Route::post('workout-hours-form-save/{id?}', [WorkoutHoursController::class, 'formSave'])->where('id', '[0-9]+')->name('workout-hours.form.save');
         Route::get('workout-hours-status/{id}', [WorkoutHoursController::class, 'changeStatus'])->where('id', '[0-9]+')->name('workout-hours.status');
         Route::get('workout-hours-delete/{id}', [WorkoutHoursController::class, 'deleteRow'])->where('id', '[0-9]+')->name('workout-hours.delete');


        /******************************-----TRANSLATION ROUTE-----************************************/
        Route::get('translate', [TranslateController::class, 'index'])->name('translate');
        Route::post('translate-data', [TranslateController::class, 'getData'])->name('translate.data');
        Route::get('translate-form/{id?}', [TranslateController::class, 'form'])->where('id', '[0-9]+')->name('translate.form');
        Route::post('translate-form-save/{id?}', [TranslateController::class, 'formSave'])->where('id', '[0-9]+')->name('translate.form.save');
        Route::get('translate-delete/{id}', [TranslateController::class, 'deleteRow'])->where('id', '[0-9]+')->name('translate.delete');

        /******************************-----SUBSCRIPTION ROUTE-----************************************/
        Route::get('subscription-plan', [SubscriptionPlanController::class, 'index'])->name('subscription-plan');
        Route::post('subscription-plan-data', [SubscriptionPlanController::class, 'getData'])->name('subscription-plan.data');
        Route::get('subscription-plan-form/{id?}', [SubscriptionPlanController::class, 'form'])->where('id', '[0-9]+')->name('subscription-plan.form');
        Route::post('subscription-plan-form-save/{id?}', [SubscriptionPlanController::class, 'formSave'])->where('id', '[0-9]+')->name('subscription-plan.form.save');
        Route::get('subscription-plan-status/{id}', [SubscriptionPlanController::class, 'changeStatus'])->where('id', '[0-9]+')->name('subscription-plan.status');
        Route::get('subscription-plan-delete/{id}', [SubscriptionPlanController::class, 'deleteRow'])->where('id', '[0-9]+')->name('subscription-plan.delete');

        /******************************-----SETTINGS ROUTE-----************************************/
        Route::get('settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('settings-save/{settingName}', [SettingsController::class, 'saveSettings'])->name('settings.save');

        /******************************-----LOG ROUTE-----************************************/
        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
    });
});




// Run if not route found
Route::any('{any}', function () {
    return redirect('/');
})->where('any', '.*');
