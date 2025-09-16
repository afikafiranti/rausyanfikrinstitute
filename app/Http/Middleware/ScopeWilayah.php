<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeWilayah
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) abort(401);

        $canOverride = $user->hasAnyRole(['admin','super_admin']);

        // Ambil wilayah diminta via route param atau query (?wilayah_id=)
        $routeWilayah = $request->route('wilayah_id') ?? $request->route('wilayah');
        $requested = $request->query('wilayah_id', $routeWilayah);

        // Tentukan wilayah efektif
        $effective = $canOverride
            ? (int)($requested ?? $user->wilayah_id)
            : (int)($user->wilayah_id);

        if (!$canOverride && $routeWilayah && (int)$routeWilayah !== $effective) {
            abort(403, 'Wilayah tidak diizinkan.');
        }

        if (!$canOverride && !$effective) {
            abort(403, 'Wilayah belum ditetapkan pada akun.');
        }

        $request->attributes->set('wilayah_id', $effective);

        return $next($request);
    }
}
