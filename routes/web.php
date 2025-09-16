<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
Route::view('/', 'dashboard')->name('dashboard');
Route::prefix('materi')->name('materi.')->group(fn() => Route::view('/', 'materi/index')->name('index'));
Route::prefix('laporan')->name('laporan.')->group(fn() => Route::view('/', 'laporan/index')->name('index'));
Route::prefix('profile')->name('profile.')->group(fn() => Route::view('/', 'profile/show')->name('show'));


// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
Route::middleware(['auth','verified'])->group(function () {
    // Contoh: nanti pindahkan dashboard ke sini bila ingin wajib verifikasi
    // Route::view('/app', 'dashboard')->name('app.dashboard');
});
Route::middleware(['auth','scope.wilayah'])->group(function () {

    // Debug scope: menampilkan wilayah efektif dari middleware
    Route::get('/debug/scope', function (Request $r) {
        return 'WILAYAH: '.$r->get('wilayah_id');
    })->name('debug.scope');

    // Halaman review laporan: hanya untuk role yang lolos Gate
    Route::get('/laporan/review', [ReportController::class, 'index'])
        ->middleware('can:review-report')
        ->name('laporan.review');
});
