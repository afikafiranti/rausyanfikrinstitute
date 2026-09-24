<?php

use App\Http\Controllers\AlumniController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Management\GalleryController;
use App\Http\Controllers\Management\PostController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('charts')->name('charts.')->group(function () {
    Route::get('/alumni/monthly', [ChartController::class, 'alumniMonthly'])->name('alumni.monthly');
    Route::get('/alumni/status',  [ChartController::class, 'alumniStatus'])->name('alumni.status');

    Route::get('/alumni/status-pernikahan', [ChartController::class, 'alumniStatusPernikahan'])
        ->name('alumni.status_pernikahan');
    Route::get('/alumni/ab', [ChartController::class, 'alumniAb'])->name('alumni.ab');
});

// LANDING PAGE
// Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::view('/', '/landingpage/home')->name('landing_page');

//  DASHBOARD
//Route::view('/dashboard', 'dashboard')->name('dashboard'); // hapus definisi '/' lain
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// NOTIFIKASI
Route::middleware(['auth'])->prefix('notifikasi')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::post('/read-all', [NotificationController::class, 'readAll'])->name('readAll');
});

// VERIFIKASI
Route::middleware(['auth', 'can:review-user'])->prefix('verifikasi')->name('verification.')->group(function () {
    Route::get('/', [VerificationController::class, 'index'])->name('index');
    Route::post('/users/{user}/approve', [VerificationController::class, 'approve'])->name('approve');
    Route::post('/users/{user}/reject',  [VerificationController::class, 'reject'])->name('reject');
});

// MATERI & LAPORAN INDEX
// Route::prefix('materi')->name('materi.')->group(fn() => Route::view('/', 'materi/index')->name('index'));
Route::middleware(['auth'])
    ->prefix('materi')
    ->name('materi.')
    ->group(function () {
        Route::get('/', [MateriController::class, 'index'])->name('index');
        Route::get('/{id}', [MateriController::class, 'show'])->name('show');
    });
Route::prefix('laporan')->name('laporan.')->group(fn() => Route::view('/', 'laporan/index')->name('index'));

// PROFIL
Route::middleware(['auth'])->group(function () {
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/update-password', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatePassword');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ALUMNI (Hari 7)

Route::middleware(['auth', 'can:view-alumni'])->get('/alumni', [\App\Http\Controllers\AlumniController::class, 'index'])->name('alumni.index');
Route::middleware(['auth', 'can:view-alumni'])->post('/alumni', [\App\Http\Controllers\AlumniController::class, 'store'])->name('alumni.store');
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/{user}/edit', [\App\Http\Controllers\AlumniController::class, 'edit'])->name('alumni.edit');
Route::middleware(['auth', 'can:view-alumni'])->patch('/alumni/{user}', [\App\Http\Controllers\AlumniController::class, 'update'])->name('alumni.update');
Route::middleware(['auth', 'can:view-alumni'])->patch('/alumni/{user}/level', [\App\Http\Controllers\AlumniController::class, 'updateLevel'])->name('alumni.level.update');
Route::middleware(['auth', 'can:view-alumni'])->delete('/alumni/{user}', [\App\Http\Controllers\AlumniController::class, 'destroy'])->name('alumni.destroy');
// DETAIL ALUMNI
Route::middleware(['auth', 'can:view-alumni'])
    ->get('/alumni/{id}', [\App\Http\Controllers\AlumniController::class, 'show'])
    ->name('alumni.show');



// SCOPE WILAYAH + REVIEW LAPORAN
Route::middleware(['auth', 'scope.wilayah'])->group(function () {
    Route::get('/debug/scope', function (Request $r) {
        return 'WILAYAH: ' . $r->get('wilayah_id');
    })->name('debug.scope');

    Route::get('/laporan/review', [ReportController::class, 'index'])
        ->middleware('can:review-report')
        ->name('laporan.review');
});

// ADMIN: POST & GALLERY 

Route::middleware(['auth', 'can:manage-content'])
    ->prefix('management')
    ->name('management.')
    ->group(function () {

        // ========== POST MANAGEMENT ==========
        Route::resource('post', PostController::class);

        // ========== GALLERY MANAGEMENT ==========
        Route::resource('galeri', GalleryController::class);
    });

require __DIR__ . '/auth.php';
