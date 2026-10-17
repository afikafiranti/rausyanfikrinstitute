<?php

namespace Tests\Support;

use RuntimeException;

final class TestDatabaseSafety
{
    public static function assertSafe(string $environment, string $connection, string $database): void
    {
        if ($environment !== 'testing'
            || ! in_array($connection, ['mysql', 'mariadb'], true)
            || ! preg_match('/\A[a-zA-Z0-9_]+_testing\z/', $database)) {
            throw new RuntimeException(
                'Tests require APP_ENV=testing, MySQL/MariaDB and a dedicated database ending in _testing.'
            );
        }
    }
}
