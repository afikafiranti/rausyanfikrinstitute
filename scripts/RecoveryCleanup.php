<?php

final class RecoveryCleanup
{
    public static function run(array $actions): void
    {
        $failed = [];
        foreach ($actions as $name => $action) {
            try {
                $action();
            } catch (Throwable) {
                $failed[] = $name;
            }
        }
        if ($failed) {
            throw new RuntimeException('Recovery cleanup failed: '.implode(', ', $failed));
        }
    }
}
