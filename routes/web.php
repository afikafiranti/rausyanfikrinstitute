<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});
Route::view('/', 'dashboard')->name('dashboard');

Route::prefix('materi')->name('materi.')->group(function () {
    Route::view('/', 'materi/index')->name('index');
});

Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::view('/', 'laporan/index')->name('index');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile/show')->name('show');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';

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
