<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\ProfessionController;
use App\Http\Controllers\CkeditorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin-panel'],function(){
    Route::get('login',[AdminDashboardController::class,'login'])->name('admin.login');
    Route::resource('admins',AdminController::class);
    Route::post('login',[AdminController::class,'verify'])->name('admin.verify');
    Route::any('forgot-password',[AdminController::class,'forgotPassword'])->name('admin.forgotPassword');
    Route::any('reset-password/{token}',[AdminController::class,'resetPassword'])->name('admin.resetPassword');

    Route::group(['middleware' => 'adminLoginAuth'],function(){
        Route::get('/logout',[AdminDashboardController::class,'logout'])->name('admin.logout');
        Route::get('/',[AdminDashboardController::class,'index'])->name('admin.dashboard');
        Route::post('ckeditor/image_upload', [CkeditorController::class,'upload'])->name('upload');
        Route::any('change-password/{id}',[AdminController::class,'changePassword'])->name('admin.changePassword');
        //Provider Routes 
        Route::get('/providers',[AdminDashboardController::class,'listProviders'])->name('admin.providers');
        Route::get('/providers/soft_delete/{id}',[AdminDashboardController::class,'softDeleteProvider'])->name('admin.providers.soft_delete');
        Route::get('/providers/restore/{id}',[AdminDashboardController::class,'restoreProvider'])->name('admin.providers.restore');
        Route::get('/providers/delete/{id}',[AdminDashboardController::class,'deleteProvider'])->name('admin.providers.delete');
        Route::patch('/providers/manage/{id}',[AdminDashboardController::class,'manageProvider'])->name('admin.providers.manage');

        //Users Routes for Admin Panel (Admin Dashboard) 
        Route::get('/users',[AdminDashboardController::class,'listUsers'])->name('admin.users');
        Route::get('/users/soft_delete/{id}',[AdminDashboardController::class,'softDeleteUser'])->name('admin.users.soft_delete');
        Route::get('/users/restore/{id}',[AdminDashboardController::class,'restoreUser'])->name('admin.users.restore');
        Route::get('/users/delete/{id}',[AdminDashboardController::class,'deleteUser'])->name('admin.users.delete');
        Route::patch('/users/manage/{id}',[AdminDashboardController::class,'manageUser'])->name('admin.users.manage');

        // Profession Route Start
        Route::resource('professions',ProfessionController::class);
        Route::get('/professions/manage/{id}',[ProfessionController::class,'manageProfession'])->name('admin.professions.manage');

        
    });
   

});
