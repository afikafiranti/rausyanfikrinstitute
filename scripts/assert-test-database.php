<?php

use Illuminate\Contracts\Console\Kernel;
use Tests\Support\TestDatabaseSafety;

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require __DIR__.'/../bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    if ($app->configurationIsCached()) {
        throw new RuntimeException('Cached configuration is not allowed for the test runner.');
    }

    $connection = $app['db']->connection();
    TestDatabaseSafety::assertSafe(
        $app->environment(),
        $connection->getDriverName(),
        $connection->getDatabaseName(),
    );
    // Check availability without creating, migrating, clearing or seeding tables.
    $connection->select('SELECT 1');
    fwrite(STDOUT, "Isolated test database configuration and connection verified.\n");
} catch (Throwable $exception) {
    // Connection exceptions can contain credentials/URLs; never print them here.
    fwrite(STDERR, "Test database preflight failed. Check test-only settings and database availability.\n");
    exit(1);
}
