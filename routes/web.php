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

Route::middleware(['auth', 'can:view-alumni', \App\Http\Middleware\ScopeWilayah::class])->prefix('charts')->name('charts.')->group(function () {
    Route::get('/alumni/monthly', [ChartController::class, 'alumniMonthly'])->name('alumni.monthly');
    Route::get('/alumni/status',  [ChartController::class, 'alumniStatus'])->name('alumni.status');

    Route::get('/alumni/status-pernikahan', [ChartController::class, 'alumniStatusPernikahan'])
        ->name('alumni.status_pernikahan');
    Route::get('/alumni/ab', [ChartController::class, 'alumniAb'])->name('alumni.ab');
});

// LANDING PAGE
Route::get('/', [LandingController::class, 'index'])->name('landing_page');
Route::get('/refleksi', [\App\Http\Controllers\LandingController::class, 'posts'])->name('public.posts.index');
Route::get('/refleksi/{slug}', [\App\Http\Controllers\LandingController::class, 'show'])->name('public.posts.show');
Route::get('/artikel', [\App\Http\Controllers\LandingController::class, 'articles'])->name('public.articles.index');
Route::get('/artikel/{slug}', [\App\Http\Controllers\LandingController::class, 'article'])->name('public.articles.show');
Route::get('/galeri', [\App\Http\Controllers\LandingController::class, 'gallery'])->name('public.gallery.index');

//  DASHBOARD
//Route::view('/dashboard', 'dashboard')->name('dashboard'); // hapus definisi '/' lain
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');


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
        Route::middleware('can:manage-content')->group(function () {
            Route::get('/kelola', [\App\Http\Controllers\MateriManagementController::class, 'index'])->name('manage');
            Route::post('/playlists', [\App\Http\Controllers\MateriManagementController::class, 'storePlaylist'])->name('playlists.store');
            Route::get('/playlists/{playlist}/edit', [\App\Http\Controllers\MateriManagementController::class, 'editPlaylist'])->name('playlists.edit');
            Route::patch('/playlists/{playlist}', [\App\Http\Controllers\MateriManagementController::class, 'updatePlaylist'])->name('playlists.update');
            Route::delete('/playlists/{playlist}', [\App\Http\Controllers\MateriManagementController::class, 'destroyPlaylist'])->name('playlists.destroy');
            Route::post('/playlists/{playlist}/videos', [\App\Http\Controllers\MateriManagementController::class, 'storeVideo'])->name('videos.store');
            Route::patch('/playlists/{playlist}/videos/{video}', [\App\Http\Controllers\MateriManagementController::class, 'updateVideo'])->name('videos.update');
            Route::delete('/playlists/{playlist}/videos/{video}', [\App\Http\Controllers\MateriManagementController::class, 'destroyVideo'])->name('videos.destroy');
        });
        Route::get('/{id}', [MateriController::class, 'show'])->name('show');
    });
Route::middleware(['auth', \App\Http\Middleware\ScopeWilayah::class])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReportController::class, 'listing'])->name('index');
    Route::get('/buat', [\App\Http\Controllers\ReportController::class, 'create'])->name('create');
    Route::get('/rekap', [\App\Http\Controllers\ReportSummaryController::class, 'index'])->middleware('can:review-report')->name('summary');
    Route::get('/ekspor', [\App\Http\Controllers\ReportSummaryController::class, 'export'])->middleware('can:review-report')->name('export');
    Route::post('/', [\App\Http\Controllers\ReportController::class, 'store'])->name('store');
    Route::get('/{report}', [\App\Http\Controllers\ReportController::class, 'show'])->whereNumber('report')->name('show');
    Route::get('/{report}/edit', [\App\Http\Controllers\ReportController::class, 'edit'])->whereNumber('report')->name('edit');
    Route::put('/{report}', [\App\Http\Controllers\ReportController::class, 'update'])->whereNumber('report')->name('update');
    Route::post('/{report}/submit', [\App\Http\Controllers\ReportController::class, 'submit'])->whereNumber('report')->name('submit');
    Route::post('/{report}/approve', [\App\Http\Controllers\ReportController::class, 'approve'])->whereNumber('report')->middleware('can:review-report')->name('approve');
    Route::post('/{report}/reject', [\App\Http\Controllers\ReportController::class, 'reject'])->whereNumber('report')->middleware('can:review-report')->name('reject');
    Route::get('/{report}/foto', [\App\Http\Controllers\ReportController::class, 'photo'])->whereNumber('report')->name('photo');
});

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
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/csv', [\App\Http\Controllers\AlumniCsvController::class, 'index'])->name('alumni.csv.index');
Route::middleware(['auth', 'can:view-alumni'])->post('/alumni/csv', [\App\Http\Controllers\AlumniCsvController::class, 'import'])->name('alumni.csv.import');
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/export', [\App\Http\Controllers\AlumniCsvController::class, 'export'])->name('alumni.csv.export');
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/template.xlsx', [\App\Http\Controllers\AlumniCsvController::class, 'template'])->name('alumni.excel.template');
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/export.xlsx', [\App\Http\Controllers\AlumniCsvController::class, 'exportExcel'])->name('alumni.excel.export');
Route::middleware(['auth', 'can:view-alumni'])->get('/alumni/{user}/edit', [\App\Http\Controllers\AlumniController::class, 'edit'])->name('alumni.edit');
Route::middleware(['auth', 'can:view-alumni'])->patch('/alumni/{user}', [\App\Http\Controllers\AlumniController::class, 'update'])->name('alumni.update');
Route::middleware(['auth', 'can:view-alumni'])->patch('/alumni/{user}/level', [\App\Http\Controllers\AlumniController::class, 'updateLevel'])->name('alumni.level.update');
Route::middleware(['auth', 'can:view-alumni'])->delete('/alumni/{user}', [\App\Http\Controllers\AlumniController::class, 'destroy'])->name('alumni.destroy');
// DETAIL ALUMNI
Route::middleware(['auth', 'can:view-alumni'])
    ->get('/alumni/{id}', [\App\Http\Controllers\AlumniController::class, 'show'])
    ->name('alumni.show');



// SCOPE WILAYAH + REVIEW LAPORAN
Route::middleware(['auth', \App\Http\Middleware\ScopeWilayah::class])->group(function () {
    Route::get('/debug/scope', function (Request $r) {
        return 'WILAYAH: ' . $r->get('wilayah_id');
    })->name('debug.scope');

    Route::get('/laporan/review', [ReportController::class, 'index'])
        ->middleware('can:review-report')
        ->name('laporan.review');
});

// ADMIN: POST & GALLERY 

Route::middleware(['auth', 'can:manage-content', \App\Http\Middleware\EnsureSuperAdmin::class])
    ->prefix('management')
    ->name('management.')
    ->group(function () {

        // ========== POST MANAGEMENT ==========
        Route::resource('post', PostController::class)->only(['index', 'store', 'update', 'destroy']);

        // ========== GALLERY MANAGEMENT ==========
        Route::resource('galeri', GalleryController::class)->only(['index', 'store', 'update', 'destroy']);
    });

require __DIR__ . '/auth.php';
