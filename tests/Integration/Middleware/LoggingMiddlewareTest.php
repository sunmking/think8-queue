<?php

namespace Sunmking\Think8Queue\Tests\Integration\Middleware;

use Sunmking\Think8Queue\Middleware\LoggingMiddleware;
use Sunmking\Think8Queue\Tests\TestCase;
use think\queue\Job;

class LoggingMiddlewareTest extends TestCase
{
    public function testSuccessfulJobLogging(): void
    {
        $middleware = new LoggingMiddleware();
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        
        $handler = function($job, $data) {
            return 'success';
        };
        
        // 捕获输出
        ob_start();
        $result = $middleware->handle($job, $data, $handler);
        $output = ob_get_clean();
        
        $this->assertEquals('success', $result);
        $this->assertStringContainsString('Job started', $output);
        $this->assertStringContainsString('Job completed', $output);
    }

    public function testFailedJobLogging(): void
    {
        $middleware = new LoggingMiddleware();
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        
        $handler = function($job, $data) {
            throw new \RuntimeException('Job failed');
        };
        
        // 捕获输出
        ob_start();
        try {
            $middleware->handle($job, $data, $handler);
        } catch (\RuntimeException $e) {
            // 预期异常
        }
        $output = ob_get_clean();
        
        $this->assertStringContainsString('Job started', $output);
        $this->assertStringContainsString('Job failed', $output);
    }

    private function createMockJob(): Job
    {
        $job = $this->createMock(Job::class);
        $job->method('attempts')->willReturn(1);
        return $job;
    }
}

