<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\CkeditorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin-panel'],function(){
    Route::get('login',[AdminDashboardController::class,'login'])->name('admin.login');
    Route::resource('admins',AdminController::class);
    Route::post('login',[AdminController::class,'verify'])->name('admin.verify');

    Route::group(['middleware' => 'adminLoginAuth'],function(){
        Route::get('/logout',[AdminDashboardController::class,'logout'])->name('admin.logout');
        Route::get('/',[AdminDashboardController::class,'index'])->name('admin.dashboard');
        Route::post('ckeditor/image_upload', [CkeditorController::class,'upload'])->name('upload');
    });
   

});
