<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class RecoveryCleanupTest extends TestCase
{
    public function test_cleanup_attempts_remaining_resources_after_failure_and_reports_failure(): void
    {
        require_once dirname(__DIR__, 2).'/scripts/RecoveryCleanup.php';
        $attempted = [];
        try {
            \RecoveryCleanup::run([
                'source database' => function () use (&$attempted) { $attempted[] = 'source'; throw new RuntimeException('simulated drop failure'); },
                'restore database' => function () use (&$attempted) { $attempted[] = 'restore'; },
                'temporary files' => function () use (&$attempted) { $attempted[] = 'files'; },
            ]);
            $this->fail('Cleanup failures must not report success.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('source database', $exception->getMessage());
            $this->assertSame(['source', 'restore', 'files'], $attempted);
        }
    }
}
