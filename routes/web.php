<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\NotificationController;

Route::middleware(['auth'])->prefix('notifikasi')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
});


Route::middleware(['auth','can:review-user'])->prefix('verifikasi')->name('verification.')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('index');
    Route::post('/users/{user}/approve', [VerificationController::class, 'approve'])->name('approve');
    Route::post('/users/{user}/reject',  [VerificationController::class, 'reject'])->name('reject');
});

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
Route::view('/', 'dashboard')->name('dashboard');
Route::prefix('materi')->name('materi.')->group(fn() => Route::view('/', 'materi/index')->name('index'));
Route::prefix('laporan')->name('laporan.')->group(fn() => Route::view('/', 'laporan/index')->name('index'));
// Route::prefix('profile')->name('profile.')->group(fn() => Route::view('/', 'profile/show')->name('show'));


Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.show');   // tampil form
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update'); // submit form
});

require __DIR__.'/auth.php';
// Route::middleware(['auth','verified'])->group(function () {
//     // Contoh: nanti pindahkan dashboard ke sini bila ingin wajib verifikasi
//     // Route::view('/app', 'dashboard')->name('app.dashboard');
// });
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
