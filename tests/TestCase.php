<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        // Runs before RefreshDatabase traits can migrate or clear any tables.
        $connection = $app['db']->connection();
        \Tests\Support\TestDatabaseSafety::assertSafe(
            $app->environment(),
            $connection->getDriverName(),
            $connection->getDatabaseName(),
        );

        return $app;
    }
}
