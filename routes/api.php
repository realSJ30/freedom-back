<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\StoresController;

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

Route::post("/register",[UserController::class,'register_user']);  


Route::group(['middleware' => 'auth:sanctum'], function(){    
    // auth logout
    // Route::post("logout",[AuthController::class,'logoutApi']);

    // USER API
    Route::group(['prefix' => 'user'], function(){
        Route::post("/create",[UserController::class,'create_user']);  
        Route::get("/all",[UserController::class,'index']);  
        Route::get("/my-profile",[UserController::class,'getProfile']);  
        Route::put("/update",[UserController::class,'update_user']);  
        Route::put("/delete/{id}",[UserController::class,'delete']);  
        Route::put("/restore/{id}",[UserController::class,'restore']);
    });

    Route::group(['prefix' => 'stores'], function(){
        Route::post("/create",[StoresController::class,'create']);  
        Route::put("/update/{id}",[StoresController::class,'update']);  
        Route::put("/delete/{id}",[StoresController::class,'delete']);  
        Route::put("/restore/{id}",[StoresController::class,'restore']);  
        Route::get("/all",[StoresController::class,'getStores']);          
    });


    // AUTHENTICATION API
    Route::post("/logout",[AuthController::class,'logout_api']);  

    
});


// unauthorized
Route::get("unauthorized",[AuthController::class,'unauthorized'])->name('unauthorized');