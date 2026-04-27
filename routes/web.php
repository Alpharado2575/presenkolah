<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

// ─── Public ───────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
Route::post('/users/tambah', [UserController::class, 'store']);

// ─── Authenticated Users (Siswa & Guru) ───────────────────────────────────────
Route::middleware(['auth', 'role:siswa,guru'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::prefix('presensi')->name('presensi.')->group(function () {
        Route::get('/',        [PresensiController::class, 'index'])->name('index');
        Route::post('/masuk',  [PresensiController::class, 'masuk'])->name('masuk');
        Route::post('/pulang', [PresensiController::class, 'pulang'])->name('pulang');
        Route::get('/riwayat',[PresensiController::class, 'riwayat'])->name('riwayat');
        Route::post('/izin',   [PresensiController::class, 'uploadIzin'])->name('izin');
    });
});

// ─── Admin ────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',    [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/users',          [AdminController::class, 'users'])->name('users');
    Route::post('/users',         [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/users/{user}',   [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}',[AdminController::class, 'destroyUser'])->name('users.destroy');

    Route::get('/presensi',              [AdminController::class, 'presensi'])->name('presensi');
    Route::get('/presensi/export',       [AdminController::class, 'exportPresensi'])->name('presensi.export');
    Route::put('/presensi/{presensi}',   [AdminController::class, 'updatePresensi'])->name('presensi.update');

    Route::get('/pengumuman',                  [AdminController::class, 'pengumuman'])->name('pengumuman');
    Route::post('/pengumuman',                 [AdminController::class, 'storePengumuman'])->name('pengumuman.store');
    Route::delete('/pengumuman/{pengumuman}',  [AdminController::class, 'destroyPengumuman'])->name('pengumuman.destroy');
});