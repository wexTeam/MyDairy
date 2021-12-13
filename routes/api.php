<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\ForgotPasswordController;
use App\Http\Controllers\api\v1\ProfileController;
use App\Http\Controllers\api\v1\UserChildController;
use App\Http\Controllers\api\v1\ChildRequestController;
use App\Http\Controllers\api\v1\SaveKilometerController;

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

Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class,'login']);
    Route::post('/register', [AuthController::class,'register']);
//    Route::post('/social', [AuthController::class,'socialLogin']);
    Route::post('/logout', [AuthController::class,'logout'])->middleware('auth:api');

    Route::post('password/forgot', [ForgotPasswordController::class,'forgot']);
    Route::post('password/reset', [ForgotPasswordController::class,'reset']);

});

Route::middleware(['auth:api','emailVerified'])->group(function () {
    // Profile
//    Route::post('profile/updateprofileimage', 'api\v1\ProfileController@updateProfileImage');
    Route::post('profile/update-password', [ProfileController::class,'updatePassword']);
    Route::post('account-information', [ProfileController::class,'updateProfile']);
    Route::get('account-information', [ProfileController::class,'userInfo']);

    //Location
//    Route::post('location', [ProfileController::class,'updateLocation']);
//    Route::get('location', [ProfileController::class,'getLocation']);

});
