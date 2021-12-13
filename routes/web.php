<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;

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

Route::get('/', function () {
    return view('welcome');
});
//
//Route::get('/login/{social}',[LoginController::class ,'socialLogin'])->where('social','facebook|google|apple');
//Route::get('/get-auth-token',[LoginController::class ,'getAuthToken']);
//Route::match(['get', 'post'], '/auth/{social}/callback',[LoginController::class ,'handleProviderCallback'])->where('social','facebook|google|apple');
//
//
//Auth::routes(['verify' => true]);
//
//Route::get('/home', [HomeController::class,'index'])->name('home');

