<?php

use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\user\UserDashboardController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'user-panel'],function(){
    Route::get('login',[UserDashboardController::class,'login'])->name('user.login');
    Route::resource('users',UserController::class);
    Route::post('login',[UserController::class,'verify'])->name('user.verify');

    Route::group(['middleware' => 'userLoginAuth'],function(){
        Route::get('/logout',[UserDashboardController::class,'logout'])->name('user.logout');
        Route::get('/',[UserDashboardController::class,'index'])->name('user.dashboard');
        Route::post('ckeditor/image_upload', [CkeditorController::class,'upload'])->name('upload');
    });
   

});