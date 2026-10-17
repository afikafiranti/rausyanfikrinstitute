<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\Support\TestDatabaseSafety;

class TestDatabaseSafetyTest extends TestCase
{
    public function test_accepts_a_dedicated_mysql_test_database(): void
    {
        TestDatabaseSafety::assertSafe('testing', 'mysql', 'rf_b_testing');
        $this->addToAssertionCount(1);
    }

    public function test_rejects_a_development_database(): void
    {
        $this->expectException(\RuntimeException::class);
        TestDatabaseSafety::assertSafe('testing', 'mysql', 'rf_db');
    }

    public function test_rejects_a_non_testing_environment(): void
    {
        $this->expectException(\RuntimeException::class);
        TestDatabaseSafety::assertSafe('production', 'mysql', 'rf_b_testing');
    }

    public function test_rejects_an_unsupported_connection(): void
    {
        $this->expectException(\RuntimeException::class);
        TestDatabaseSafety::assertSafe('testing', 'sqlite', 'rf_b_testing');
    }

    public function test_accepts_a_dedicated_mariadb_test_database(): void
    {
        TestDatabaseSafety::assertSafe('testing', 'mariadb', 'rf_b_testing');
        $this->addToAssertionCount(1);
    }
}
