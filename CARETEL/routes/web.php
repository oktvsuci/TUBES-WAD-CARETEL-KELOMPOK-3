<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\LaporanAdminController;
use App\Http\Controllers\Admin\PenugasanController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\MonitoringController;

use App\Http\Controllers\Mahasiswa\DashboardMahasiswaController;
use App\Http\Controllers\Mahasiswa\LaporanMahasiswaController;
use App\Http\Controllers\Mahasiswa\ProfilController;
use App\Http\Controllers\Mahasiswa\TrackingController;
use App\Http\Controllers\Mahasiswa\RiwayatController;
use App\Http\Controllers\Mahasiswa\NotifikasiController;

use App\Http\Controllers\Teknisi\DashboardTeknisiController;
use App\Http\Controllers\Teknisi\TugasController;
use App\Http\Controllers\Teknisi\StatusController;
use App\Http\Controllers\Teknisi\DokumentasiController;
use App\Http\Controllers\Teknisi\RiwayatTeknisiController;
use App\Http\Controllers\Teknisi\ProfilTeknisiController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES - COMPLETE VERSION
|--------------------------------------------------------------------------
*/

// =====================
// PUBLIC ROUTES
// =====================
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/login', function () {
    return view('Auth.Login');
})->name('login');

// =====================
// ADMIN ROUTES
// =====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardAdminController::class, 'export'])->name('dashboard.export');
    
    // Laporan Management (CRUD)
    Route::get('/laporan', [LaporanAdminController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanAdminController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanAdminController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanAdminController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{id}/edit', [LaporanAdminController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{id}', [LaporanAdminController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{id}', [LaporanAdminController::class, 'destroy'])->name('laporan.destroy');
    Route::post('/laporan/bulk-delete', [LaporanAdminController::class, 'bulkDelete'])->name('laporan.bulkDelete');
    Route::get('/laporan-export', [LaporanAdminController::class, 'export'])->name('laporan.export');
    
    // Penugasan Management (CRUD)
    Route::get('/penugasan', [PenugasanController::class, 'index'])->name('penugasan.index');
    Route::get('/penugasan/create', [PenugasanController::class, 'create'])->name('penugasan.create');
    Route::post('/penugasan', [PenugasanController::class, 'store'])->name('penugasan.store');
    Route::get('/penugasan/{id}', [PenugasanController::class, 'show'])->name('penugasan.show');
    Route::get('/penugasan/{id}/edit', [PenugasanController::class, 'edit'])->name('penugasan.edit');
    Route::put('/penugasan/{id}', [PenugasanController::class, 'update'])->name('penugasan.update');
    Route::delete('/penugasan/{id}', [PenugasanController::class, 'destroy'])->name('penugasan.destroy');
    Route::post('/penugasan/{id}/reassign', [PenugasanController::class, 'reassign'])->name('penugasan.reassign');
    Route::post('/penugasan/{id}/extend-deadline', [PenugasanController::class, 'extendDeadline'])->name('penugasan.extendDeadline');
    Route::post('/penugasan/bulk-assign', [PenugasanController::class, 'bulkAssign'])->name('penugasan.bulkAssign');
    Route::get('/penugasan/teknisi-availability', [PenugasanController::class, 'teknisiAvailability'])->name('penugasan.teknisiAvailability');
    Route::get('/penugasan-export', [PenugasanController::class, 'export'])->name('penugasan.export');
    
    // User Management (CRUD)
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserManagementController::class, 'update'])->name('users.update');
    Route::post('/users/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.resetPassword');
    Route::post('/users/{id}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-action', [UserManagementController::class, 'bulkAction'])->name('users.bulkAction');
    Route::get('/users-export', [UserManagementController::class, 'export'])->name('users.export');
    
    // Kategori Management (CRUD)
    Route::get('/kategori', [KategoriController::class, 'index'])->name('kategori.index');
    Route::post('/kategori', [KategoriController::class, 'store'])->name('kategori.store');
    Route::put('/kategori/{id}', [KategoriController::class, 'update'])->name('kategori.update');
    Route::delete('/kategori/{id}', [KategoriController::class, 'destroy'])->name('kategori.destroy');
    
    // Monitoring
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::post('/monitoring/{id}/update-status', [MonitoringController::class, 'updateStatus'])->name('monitoring.updateStatus');
});

// =====================
// MAHASISWA ROUTES
// =====================
Route::prefix('mahasiswa')->name('mahasiswa.')->middleware(['auth', 'role:mahasiswa'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardMahasiswaController::class, 'index'])->name('dashboard');
    
    // Laporan (CRUD)
    Route::get('/laporan', [LaporanMahasiswaController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create', [LaporanMahasiswaController::class, 'create'])->name('laporan.create');
    Route::post('/laporan', [LaporanMahasiswaController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{id}', [LaporanMahasiswaController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{id}/edit', [LaporanMahasiswaController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{id}', [LaporanMahasiswaController::class, 'update'])->name('laporan.update');
    Route::delete('/laporan/{id}', [LaporanMahasiswaController::class, 'destroy'])->name('laporan.destroy');
    
    // Tracking
    Route::get('/tracking', [TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/{id}', [TrackingController::class, 'show'])->name('tracking.show');
    Route::post('/tracking/{id}/comment', [TrackingController::class, 'addComment'])->name('tracking.addComment');
    
    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatController::class, 'show'])->name('riwayat.show');
    Route::post('/riwayat/{laporanId}/rating', [RiwayatController::class, 'submitRating'])->name('riwayat.submitRating');
    Route::put('/riwayat/rating/{ratingId}', [RiwayatController::class, 'updateRating'])->name('riwayat.updateRating');
    Route::get('/riwayat/statistik', [RiwayatController::class, 'statistik'])->name('riwayat.statistik');
    
    // Profil
    Route::get('/profil', [ProfilController::class, 'show'])->name('profil.show');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/deactivate', [ProfilController::class, 'deactivate'])->name('profil.deactivate');
    
    // Notifikasi (API-style routes in web)
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'show'])->name('notifikasi.show');
    Route::post('/notifikasi/{id}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
    Route::delete('/notifikasi/delete-read', [NotifikasiController::class, 'deleteRead'])->name('notifikasi.deleteRead');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'unreadCount'])->name('notifikasi.unreadCount');
});

// =====================
// TEKNISI ROUTES
// =====================
Route::prefix('teknisi')->name('teknisi.')->middleware(['auth', 'role:teknisi'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardTeknisiController::class, 'index'])->name('dashboard');
    
    // Tugas (CRUD)
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{id}', [TugasController::class, 'show'])->name('tugas.show');
    Route::post('/tugas/{id}/terima', [TugasController::class, 'terima'])->name('tugas.terima');
    Route::post('/tugas/{id}/tolak', [TugasController::class, 'tolak'])->name('tugas.tolak');
    Route::put('/tugas/{id}/estimasi', [TugasController::class, 'updateEstimasi'])->name('tugas.updateEstimasi');
    
    // Status Update (CRUD)
    Route::get('/status', [StatusController::class, 'index'])->name('status.index');
    Route::get('/status/{id}/edit', [StatusController::class, 'edit'])->name('status.edit');
    Route::put('/status/{id}', [StatusController::class, 'update'])->name('status.update');
    Route::post('/status/{id}/progress', [StatusController::class, 'createProgress'])->name('status.createProgress');
    Route::get('/status/{id}/history', [StatusController::class, 'history'])->name('status.history');
    
    // Dokumentasi (CRUD)
    Route::get('/dokumentasi', [DokumentasiController::class, 'index'])->name('dokumentasi.index');
    Route::get('/dokumentasi/{id}/edit', [DokumentasiController::class, 'edit'])->name('dokumentasi.edit');
    Route::put('/dokumentasi/{id}', [DokumentasiController::class, 'update'])->name('dokumentasi.update');
    
    // Riwayat (READ)
    Route::get('/riwayat', [RiwayatTeknisiController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatTeknisiController::class, 'show'])->name('riwayat.show');
    Route::put('/riwayat/{id}', [RiwayatTeknisiController::class, 'update'])->name('riwayat.update');
    
    // Profil (CRUD)
    Route::get('/profil', [ProfilTeknisiController::class, 'index'])->name('profil.index');
    Route::get('/profil/edit', [ProfilTeknisiController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfilTeknisiController::class, 'update'])->name('profil.update');
    Route::get('/profil/edit-password', [ProfilTeknisiController::class, 'editPassword'])->name('profil.editPassword');
    Route::put('/profil/password', [ProfilTeknisiController::class, 'updatePassword'])->name('profil.updatePassword');
    Route::delete('/profil/foto', [ProfilTeknisiController::class, 'deleteFoto'])->name('profil.deleteFoto');
    Route::get('/profil/aktivitas', [ProfilTeknisiController::class, 'aktivitas'])->name('profil.aktivitas');
    Route::get('/profil/rating', [ProfilTeknisiController::class, 'rating'])->name('profil.rating');
});