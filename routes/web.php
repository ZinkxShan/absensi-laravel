<?php
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Login & Logout
Route::get('/login',   [AuthController::class, 'halamanLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User & Admin — scan masuk keluar (sekretaris TIDAK boleh)
Route::middleware(['auth', 'auth.scan'])->group(function () {
    Route::get('/', fn() => redirect('/masuk'));
    Route::get('/masuk',  [AbsensiController::class, 'halamanMasuk']);
    Route::get('/keluar', [AbsensiController::class, 'halamanKeluar']);
    Route::post('/api/scan/masuk',  [AbsensiController::class, 'scanMasuk']);
    Route::post('/api/scan/keluar', [AbsensiController::class, 'scanKeluar']);
});

// Admin & Sekretaris — dashboard
Route::middleware(['auth', 'auth.sekretaris'])->group(function () {
    Route::get('/dashboard',               [AbsensiController::class, 'halamanDashboard']);
    Route::get('/api/dashboard',           [AbsensiController::class, 'apiDashboard']);
    Route::post('/api/keterangan',         [AbsensiController::class, 'inputKeterangan']);
    Route::get('/api/settings/jam-batas',  [AbsensiController::class, 'getJamBatasApi']);
});

// Admin only
Route::middleware(['auth', 'auth.admin'])->group(function () {
    Route::get('/kelola',                  [AbsensiController::class, 'halamanKelola']);
    Route::get('/api/siswa',               [AbsensiController::class, 'getSiswa']);
    Route::post('/api/siswa',              [AbsensiController::class, 'tambahSiswa']);
    Route::delete('/api/siswa/{id}',       [AbsensiController::class, 'hapusSiswa']);
    Route::get('/api/qrcode/{kode}',       [AbsensiController::class, 'getQrCode']);
    Route::get('/kartu/{id}',              [AbsensiController::class, 'downloadKartu']);
    Route::post('/api/settings/jam-batas', [AbsensiController::class, 'setJamBatas']);
    Route::get('/api/users',               [AbsensiController::class, 'getUsers']);
    Route::post('/api/users',              [AbsensiController::class, 'tambahUser']);
    Route::delete('/api/users/{id}',       [AbsensiController::class, 'hapusUser']);
});