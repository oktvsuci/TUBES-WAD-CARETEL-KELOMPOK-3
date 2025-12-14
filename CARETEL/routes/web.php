<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\LaporanAdminController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Mahasiswa\DashboardMahasiswa;
use App\Http\Controllers\Mahasiswa\LaporanMahasiswa;
use App\Http\Controllers\Teknisi\TrackingController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES
|--------------------------------------------------------------------------
*/

// =====================
// AUTH
// =====================
Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('Auth.Login');
});

// =====================
// ADMIN
// =====================
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardAdminController::class, 'index']);

    Route::get('/laporan', [LaporanAdminController::class, 'index']);
    Route::get('/laporan/{id}', [LaporanAdminController::class, 'show']);
    Route::get('/laporan/{id}/history', [LaporanAdminController::class, 'history']);

    Route::get('/penugasan', [PenugasanController::class, 'index']);
});

// =====================
// MAHASISWA
// =====================
Route::prefix('mahasiswa')->group(function () {

    Route::get('/dashboard', [DashboardMahasiswa::class, 'index']);
    Route::get('/laporan', [LaporanMahasiswa::class, 'index']);
});

// =====================
// TEKNISI
// =====================
Route::prefix('teknisi')->group(function () {

    Route::get('/dashboard', function () {
        return view('Teknisi.dashboard');
    });

    Route::get('/tugas', function () {
        return view('Teknisi.Tugas.index');
    });

    Route::get('/riwayat', function () {
        return view('Teknisi.Riwayat.index');
    });

});
