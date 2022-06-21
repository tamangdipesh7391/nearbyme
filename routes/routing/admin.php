<?php

use App\Http\Controllers\admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'admin','prefix' => 'admin'],function(){
    // Route::get('/',[AdminController::class,'index'])->name('admin');
    Route::get('/',function(){
        return "hi";
    });
});