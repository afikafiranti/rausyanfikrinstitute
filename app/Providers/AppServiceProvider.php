<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');
            return [
                Limit::perMinute(5)->by($email.$request->ip())->response(function () {
                    return response()->json([
                        'message' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.'
                    ], 429);
                }),
            ];
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }
        

    }
}
