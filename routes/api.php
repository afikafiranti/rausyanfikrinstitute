<?php

use Illuminate\Support\Facades\Route;

// Endpoint kesehatan sederhana (tanpa Sanctum)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app'    => 'Rausyan Fikr',
        'time'   => now()->toDateTimeString(),
    ]);
});
