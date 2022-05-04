<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\StoresController;
use App\Http\Controllers\Api\RolesController;

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

    // fetch authenticated user
    Route::get("/me",[AuthController::class,'get_authenticated_user']);  

    // USER API
    Route::group(['prefix' => 'user'], function(){
        Route::post("/create",[UserController::class,'create_user']);  
        Route::get("/all",[UserController::class,'index']);          
        Route::put("/update",[UserController::class,'update_user']);  
        Route::put("/delete/{id}",[UserController::class,'delete']);  
        Route::put("/restore/{id}",[UserController::class,'restore']);
        Route::put("/update-role/{id}",[UserController::class,'update_user_role']);        
    });

    Route::group(['prefix' => 'stores'], function(){
        Route::post("/create",[StoresController::class,'create']);  
        Route::put("/update/{id}",[StoresController::class,'update']);  
        Route::put("/delete/{id}",[StoresController::class,'delete']);  
        Route::put("/restore/{id}",[StoresController::class,'restore']);  
        Route::get("/all",[StoresController::class,'get_stores']);          
        Route::get("/{id}",[StoresController::class,'get_store']);          
    });

    // ROLES
    Route::group(['prefix' => 'roles'], function(){          
        Route::get("/all",[RolesController::class,'index']);                 
    });


    // AUTHENTICATION API
    Route::post("/logout",[AuthController::class,'logout_api']);  

    
});


// unauthorized
Route::get("unauthorized",[AuthController::class,'unauthorized'])->name('unauthorized');