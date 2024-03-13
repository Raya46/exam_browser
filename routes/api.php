<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'postLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/links', [LinkController::class, 'index']);
    Route::get('/links/{id}', [LinkController::class, 'show']);
    Route::post('/links-post', [LinkController::class, 'store']);
    Route::delete('/links-delete/{id}', [LinkController::class, 'destroy']);
    Route::put('/links-put/{id}', [LinkController::class, 'putLink']);
});
