<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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


// authentication
Route::post("login",[AuthController::class,'login']);
Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::post("logout",[AuthController::class,'logout']);
});

// postman add on header for testing X-CSRF-TOKEN
Route::get("csrf-token",[AuthController::class,'csrf']);
