<?php

namespace Sunmking\Think8Queue\Tests\Helpers;

use Sunmking\Think8Queue\Jobs\AbstractJob;
use think\queue\Job;

class TestJob extends AbstractJob
{
    public static array $executedJobs = [];
    public static int $executionCount = 0;
    public static bool $shouldFail = false;
    public static string $failureMessage = 'Test failure';

    public static function reset(): void
    {
        self::$executedJobs = [];
        self::$executionCount = 0;
        self::$shouldFail = false;
        self::$failureMessage = 'Test failure';
    }

    protected function execute(Job $job, array $data): void
    {
        self::$executionCount++;
        self::$executedJobs[] = $data;

        if (self::$shouldFail) {
            throw new \RuntimeException(self::$failureMessage);
        }

        // 模拟一些工作
        usleep(1000); // 1ms
    }
}

