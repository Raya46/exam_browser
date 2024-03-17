<?php

use App\Http\Controllers\PayController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/pay', [PayController::class, 'pay']);
