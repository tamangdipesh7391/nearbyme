<?php

use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\provider\ProviderController;
use App\Http\Controllers\provider\ProviderDashboardController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'provider-panel'],function(){
    Route::get('login',[ProviderDashboardController::class,'login'])->name('provider.login');
    Route::resource('providers',ProviderController::class);
    Route::post('login',[ProviderController::class,'verify'])->name('provider.verify');

    Route::group(['middleware' => 'providerLoginAuth'],function(){
        Route::get('/logout',[ProviderDashboardController::class,'logout'])->name('provider.logout');
        Route::get('/',[ProviderDashboardController::class,'index'])->name('provider.dashboard');
        Route::post('ckeditor/image_upload', [CkeditorController::class,'upload'])->name('upload');
    });
   

});