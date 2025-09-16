<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

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
