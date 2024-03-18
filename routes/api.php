<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'postLogin']);
Route::post('/pay/hook', [PayController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pay', [PayController::class, 'pay']);
    Route::get('/subscription', [SubscriptionController::class, 'index']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/links', [LinkController::class, 'index']);
    Route::prefix('links')->group(function (){
        Route::get('/{id}', [LinkController::class, 'show']);
        Route::post('/post', [LinkController::class, 'store']);
        Route::delete('/{id}', [LinkController::class, 'destroy']);
        Route::put('/{id}', [LinkController::class, 'putLink']);
    });
    Route::get('/super-admin', [UserController::class, 'indexSuperAdmin']);
    Route::prefix('super-admin')->group(function (){
        Route::get('/{id}', [UserController::class, 'showUser']);
        Route::post('/post', [UserController::class, 'createAdminSekolah']);
        Route::delete('/{id}', [UserController::class, 'deleteAdminSekolah']);
        Route::put('/{id}', [UserController::class, 'updateAdminSekolah']);
    });
    Route::get('/admin-sekolah', [UserController::class, 'indexAdminSekolah']);
    Route::prefix('admin-sekolah')->group(function (){
        Route::get('/{id}', [UserController::class, 'showUser']);
        Route::post('/post', [UserController::class, 'createSiswa']);
        Route::delete('/{id}', [UserController::class, 'deleteSiswa']);
        Route::put('/{id}', [UserController::class, 'updateSiswa']);
    });
    Route::prefix('subscription')->group(function (){
        Route::get('/{id}', [SubscriptionController::class, 'show']);
        Route::post('/post', [SubscriptionController::class, 'store']);
        Route::delete('/{id}', [SubscriptionController::class, 'destroy']);
        Route::put('/{id}', [SubscriptionController::class, 'putSubscription']);
    });
});
