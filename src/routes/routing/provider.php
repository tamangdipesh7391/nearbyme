<?php

use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\provider\ProviderController;
use App\Http\Controllers\provider\ProviderDashboardController;
use App\Http\Controllers\provider\RequestedServiceController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'provider-panel'],function(){
    Route::get('login',[ProviderDashboardController::class,'login'])->name('provider.login');
    Route::resource('providers',ProviderController::class);
    Route::post('login',[ProviderController::class,'verify'])->name('provider.verify');

    Route::group(['middleware' => 'providerLoginAuth'],function(){
        Route::any('change-password/{id}',[ProviderController::class,'changePassword'])->name('provider.changePassword');

        Route::get('/logout',[ProviderDashboardController::class,'logout'])->name('provider.logout');
        Route::get('/',[ProviderDashboardController::class,'index'])->name('provider.dashboard');
        Route::post('ckeditor/image_upload', [CkeditorController::class,'upload'])->name('upload');
        Route::get('/request-list/{id}/{hid?}',[RequestedServiceController::class,'requestList'])->name('provider.requestList');
        Route::get('/request-list/soft_delete/{id}',[RequestedServiceController::class,'softDeleteRequest'])->name('provider.request.soft_delete');
        Route::get('/request-list/restore/{id}',[RequestedServiceController::class,'restoreRequest'])->name('provider.request.restore');
        Route::get('/request-list/delete/{id}',[RequestedServiceController::class,'deleteRequest'])->name('provider.request.delete');
        Route::patch('/request/manage/{id}',[RequestedServiceController::class,'manageRequest'])->name('provider.request.manage');

    });
   

});