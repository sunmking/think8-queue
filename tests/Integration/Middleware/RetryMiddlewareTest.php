<?php

namespace Sunmking\Think8Queue\Tests\Integration\Middleware;

use Sunmking\Think8Queue\Middleware\RetryMiddleware;
use Sunmking\Think8Queue\Tests\TestCase;
use think\queue\Job;

class RetryMiddlewareTest extends TestCase
{
    public function testSuccessfulJobNoRetry(): void
    {
        $middleware = new RetryMiddleware();
        $job = $this->createMockJob(1);
        $data = ['attempts' => 3];
        
        $handler = function($job, $data) {
            return 'success';
        };
        
        $result = $middleware->handle($job, $data, $handler);
        
        $this->assertEquals('success', $result);
    }

    public function testFailedJobWithinRetryLimit(): void
    {
        $middleware = new RetryMiddleware();
        $job = $this->createMockJob(2);
        $data = ['attempts' => 3, 'retry_after' => 60];
        
        $handler = function($job, $data) {
            throw new \RuntimeException('Job failed');
        };
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Job failed');
        
        $middleware->handle($job, $data, $handler);
    }

    public function testFailedJobExceedsRetryLimit(): void
    {
        $middleware = new RetryMiddleware();
        $job = $this->createMockJob(3);
        $data = ['attempts' => 3];
        
        $handler = function($job, $data) {
            throw new \RuntimeException('Job failed');
        };
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Job failed');
        
        $middleware->handle($job, $data, $handler);
    }

    private function createMockJob(int $attempts): Job
    {
        $job = $this->createMock(Job::class);
        $job->method('attempts')->willReturn($attempts);
        return $job;
    }
}

