<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

final class PublicImageUrl
{
    public static function resolve(?string $path, string $directory): ?string
    {
        if (! $path) {
            return null;
        }
        if (filter_var($path, FILTER_VALIDATE_URL)
            && in_array(strtolower((string) parse_url($path, PHP_URL_SCHEME)), ['http', 'https'], true)) {
            return $path;
        }

        $path = preg_replace('#^/?storage/#', '', $path);
        if (str_starts_with($path, $directory.'/')
            && ! str_contains($path, '..')
            && preg_match('#\A[a-zA-Z0-9_./-]+\z#', $path)) {
            return Storage::disk('public')->url($path);
        }

        return null;
    }
}
