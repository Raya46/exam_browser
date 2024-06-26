<?php

use App\Http\Controllers\LinkController;
use App\Http\Controllers\PayController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\KelasJurusanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserController::class, 'loginAdmin']);
Route::post('/login-siswa', [UserController::class, 'loginSiswaAdmin']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/item', [ItemController::class, 'index']);
Route::get('/sekolah', [UserController::class, 'getSekolah']);
Route::get('/kelas-jurusan', [UserController::class, 'getKelasJurusan']);
Route::put('/update-status', [ProgressController::class, 'updateStatusByTime']);
Route::post('/pay/hook', [PayController::class, 'webhook']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/verify', [UserController::class, 'updateOrVerifySerialNumber']);
    Route::get('/get-data-login', [UserController::class, 'getDataLoggedIn']);
    Route::get('/links', [LinkController::class, 'index']);
    Route::post('/links/post', [LinkController::class, 'storeLink']);
    Route::prefix('links')->group(function (){
        Route::get('/{id}', [LinkController::class, 'show']);
        Route::delete('/{id}', [LinkController::class, 'destroy']);
        Route::put('/{id}', [LinkController::class, 'putLink']);
    });
    Route::get('/super-admin/list-pay', [PayController::class, 'getUserSubs']);
    Route::get('/super-admin', [UserController::class, 'indexSuperAdmin']);
    Route::prefix('super-admin')->group(function (){
        Route::get('/{id}', [UserController::class, 'showUser']);
        Route::post('/post', [UserController::class, 'createSiswaAdminSekolah']);
        Route::delete('/{id}', [UserController::class, 'deleteSiswaAdminSekolah']);
        Route::put('/{id}', [UserController::class, 'updateSiswaAdminSekolah']);
    });
    Route::get('/admin-sekolah/siswa-export', [UserController::class, 'export_siswa_excel']);
    Route::get('/admin-sekolah/links', [LinkController::class, 'indexLinkAdmin']);
    Route::get('/admin-sekolah/monitoring', [ProgressController::class, 'monitoringUserProgress']);
    Route::post('/admin-sekolah/siswa-import', [UserController::class, 'import_siswa_excel']);
    Route::get('/admin-sekolah', [UserController::class, 'indexAdminSekolah']);
    Route::get('/admin-sekolah/kelas-jurusan', [UserController::class, 'getKelasJurusanLog']);
    Route::prefix('admin-sekolah')->group(function (){
        Route::get('/{id}', [UserController::class, 'showUser']);
        Route::post('/post', [UserController::class, 'createSiswaAdminSekolah']);
        Route::delete('/{id}', [UserController::class, 'deleteSiswaAdminSekolah']);
        Route::put('/{id}', [UserController::class, 'updateSiswaAdminSekolah']);
    });
    Route::get('/progress', [ProgressController::class, 'userProgress']);
    Route::put('/progress/user/{id}', [ProgressController::class, 'progressUser']);
    Route::prefix('progress')->group(function (){
        Route::get('/{id}', [ProgressController::class, 'show']);
        Route::post('/post', [ProgressController::class, 'createOrUpdateProgress']);
        Route::put('/{id}', [ProgressController::class, 'updateProgress']);
    });
    Route::prefix('item')->group(function (){
        Route::get('/{id}', [ItemController::class, 'show']);
        Route::post('/post', [ItemController::class, 'store']);
        Route::delete('/{id}', [ItemController::class, 'destroy']);
        Route::put('/{id}', [ItemController::class, 'putItem']);
    });
    Route::post('/pay', [PayController::class, 'pay']);
    Route::get('/get-pay/{id}', [PayController::class, 'getPay']);
    Route::post('/post-kelas', [KelasJurusanController::class, 'store']);
    Route::get('/get-kelas', [KelasJurusanController::class, 'index']);
    Route::get('/get-kelas/{id}', [KelasJurusanController::class, 'show']);
    Route::put('/update-kelas/{id}', [KelasJurusanController::class, 'updateKelasJurusan']);
    Route::delete('/delete-kelas/{id}', [KelasJurusanController::class, 'destroy']);
});
