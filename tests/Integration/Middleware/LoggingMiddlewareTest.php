<?php

namespace Sunmking\Think8Queue\Tests\Integration\Middleware;

use Sunmking\Think8Queue\Middleware\LoggingMiddleware;
use Sunmking\Think8Queue\Tests\TestCase;
use think\queue\Job;

class LoggingMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 重置 MockLog 的消息
        if (class_exists('MockLog')) {
            \MockLog::$messages = [];
        }
    }

    public function testSuccessfulJobLogging()
    {
        $middleware = new LoggingMiddleware();
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        
        $handler = function($job, $data) {
            return 'success';
        };
        
        // 重置日志消息
        \MockLog::$messages = [];
        
        $result = $middleware->handle($job, $data, $handler);
        
        $this->assertEquals('success', $result);
        $this->assertStringContainsString('Job started', implode(' ', \MockLog::$messages));
        $this->assertStringContainsString('Job completed', implode(' ', \MockLog::$messages));
    }

    public function testFailedJobLogging()
    {
        $middleware = new LoggingMiddleware();
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        
        $handler = function($job, $data) {
            throw new \RuntimeException('Job failed');
        };
        
        // 重置日志消息
        \MockLog::$messages = [];
        
        try {
            $middleware->handle($job, $data, $handler);
        } catch (\RuntimeException $e) {
            // 预期异常
        }
        
        $this->assertStringContainsString('Job started', implode(' ', \MockLog::$messages));
        $this->assertStringContainsString('Job failed', implode(' ', \MockLog::$messages));
    }

    private function createMockJob(): Job
    {
        $job = $this->createMock(Job::class);
        $job->method('attempts')->willReturn(1);
        return $job;
    }
}

