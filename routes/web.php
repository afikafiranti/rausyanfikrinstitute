<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AlumniController;
use App\Http\Controllers\ChartController;

Route::middleware(['auth'])->prefix('charts')->name('charts.')->group(function () {
    Route::get('/alumni/monthly', [ChartController::class, 'alumniMonthly'])->name('alumni.monthly');
    Route::get('/alumni/status',  [ChartController::class, 'alumniStatus'])->name('alumni.status');
});


// HOME
Route::view('/', 'dashboard')->name('dashboard'); // hapus definisi '/' lain

// NOTIFIKASI
Route::middleware(['auth'])->prefix('notifikasi')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
});

// VERIFIKASI
Route::middleware(['auth','can:review-user'])->prefix('verifikasi')->name('verification.')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('index');
    Route::post('/users/{user}/approve', [VerificationController::class, 'approve'])->name('approve');
    Route::post('/users/{user}/reject',  [VerificationController::class, 'reject'])->name('reject');
});

// MATERI & LAPORAN INDEX
Route::prefix('materi')->name('materi.')->group(fn() => Route::view('/', 'materi/index')->name('index'));
Route::prefix('laporan')->name('laporan.')->group(fn() => Route::view('/', 'laporan/index')->name('index'));

// PROFIL
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ALUMNI (Hari 7)

Route::middleware(['auth','can:view-alumni'])->get('/alumni', [\App\Http\Controllers\AlumniController::class, 'index'])->name('alumni.index');
// DETAIL ALUMNI
Route::middleware(['auth','can:view-alumni'])
    ->get('/alumni/{id}', [\App\Http\Controllers\AlumniController::class, 'show'])
    ->name('alumni.show');



// SCOPE WILAYAH + REVIEW LAPORAN
Route::middleware(['auth','scope.wilayah'])->group(function () {
    Route::get('/debug/scope', function (Request $r) {
        return 'WILAYAH: '.$r->get('wilayah_id');
    })->name('debug.scope');

    Route::get('/laporan/review', [ReportController::class, 'index'])
        ->middleware('can:review-report')
        ->name('laporan.review');
});

require __DIR__.'/auth.php';
