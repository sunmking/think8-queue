<?php

namespace Sunmking\Think8Queue\Tests\Integration\Jobs;

use Sunmking\Think8Queue\Jobs\AbstractJob;
use Sunmking\Think8Queue\Tests\TestCase;
use think\queue\Job;

class AbstractJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // mock log 门面
        if (!class_exists('log')) {
            class_alias(\Sunmking\Think8Queue\Tests\Stubs\LogStub::class, 'log');
        }
    }

    public function testJobExecution(): void
    {
        $job = new TestJob();
        $mockJob = $this->createMock(Job::class);
        $data = ['test' => 'data'];
        
        $job->handle($mockJob, $data);
        
        $this->assertTrue(TestJob::$executed);
        $this->assertEquals($data, TestJob::$executedData);
    }

    public function testJobFailure(): void
    {
        $job = new FailingJob();
        $mockJob = $this->createMock(Job::class);
        $data = ['test' => 'data'];
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Job failed');
        
        $job->handle($mockJob, $data);
    }

    public function testFailedMethod(): void
    {
        $job = new TestJob();
        $data = ['test' => 'data'];
        $exception = new \RuntimeException('Test exception');
        
        $job->failed($data, $exception);
        
        $this->assertTrue(TestJob::$failedCalled);
        $this->assertEquals($data, TestJob::$failedData);
        $this->assertEquals($exception, TestJob::$failedException);
    }

    public function testMiddlewareIntegration(): void
    {
        $job = new TestJobWithMiddleware();
        $mockJob = $this->createMock(Job::class);
        $data = ['test' => 'data'];
        
        $job->handle($mockJob, $data);
        
        $this->assertTrue(TestJobWithMiddleware::$executed);
        $this->assertTrue(TestJobWithMiddleware::$middlewareCalled);
    }

    public function testEventIntegration(): void
    {
        $job = new TestJobWithEvents();
        $mockJob = $this->createMock(Job::class);
        $data = ['test' => 'data'];
        
        $job->handle($mockJob, $data);
        
        $this->assertTrue(TestJobWithEvents::$executed);
        $this->assertTrue(TestJobWithEvents::$processingEventCalled);
        $this->assertTrue(TestJobWithEvents::$processedEventCalled);
    }
}

class TestJob extends AbstractJob
{
    public static bool $executed = false;
    public static array $executedData = [];
    public static bool $failedCalled = false;
    public static array $failedData = [];
    public static ?\Throwable $failedException = null;

    protected function execute(Job $job, array $data): void
    {
        self::$executed = true;
        self::$executedData = $data;
    }
}

class FailingJob extends AbstractJob
{
    protected function execute(Job $job, array $data): void
    {
        throw new \RuntimeException('Job failed');
    }
}

class TestJobWithMiddleware extends AbstractJob
{
    public static bool $executed = false;
    public static bool $middlewareCalled = false;

    public function __construct()
    {
        parent::__construct();
        $this->middleware(TestMiddleware::class);
    }

    protected function execute(Job $job, array $data): void
    {
        self::$executed = true;
    }
}

class TestJobWithEvents extends AbstractJob
{
    public static bool $executed = false;
    public static bool $processingEventCalled = false;
    public static bool $processedEventCalled = false;

    public function __construct()
    {
        parent::__construct();
        
        $this->on(\Sunmking\Think8Queue\Events\JobEvent\JobProcessing::class, function() {
            self::$processingEventCalled = true;
        });
        
        $this->on(\Sunmking\Think8Queue\Events\JobEvent\JobProcessed::class, function() {
            self::$processedEventCalled = true;
        });
    }

    protected function execute(Job $job, array $data): void
    {
        self::$executed = true;
    }
}

class TestMiddleware implements \Sunmking\Think8Queue\Middleware\MiddlewareInterface
{
    public function handle(Job $job, array $data, callable $next): mixed
    {
        TestJobWithMiddleware::$middlewareCalled = true;
        return $next($job, $data);
    }
}

