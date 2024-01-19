<?php

use App\Http\Controllers\LinkController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/links', [LinkController::class, 'index']);
