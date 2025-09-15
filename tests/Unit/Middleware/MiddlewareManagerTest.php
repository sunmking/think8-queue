<?php

namespace Sunmking\Think8Queue\Tests\Unit\Middleware;

use Sunmking\Think8Queue\Middleware\MiddlewareInterface;
use Sunmking\Think8Queue\Middleware\MiddlewareManager;
use Sunmking\Think8Queue\Tests\TestCase;
use think\queue\Job;

class MiddlewareManagerTest extends TestCase
{
    private MiddlewareManager $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new MiddlewareManager();
    }

    public function testAddMiddleware(): void
    {
        $middleware = new TestMiddleware();
        $result = $this->manager->add($middleware);
        
        $this->assertSame($this->manager, $result);
    }

    public function testAddMiddlewareByClass(): void
    {
        $result = $this->manager->add(TestMiddleware::class);
        
        $this->assertSame($this->manager, $result);
    }

    public function testProcessWithoutMiddleware(): void
    {
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        $handler = function($job, $data) {
            return 'processed';
        };
        
        $result = $this->manager->process($job, $data, $handler);
        
        $this->assertEquals('processed', $result);
    }

    public function testProcessWithSingleMiddleware(): void
    {
        $middleware = new TestMiddleware();
        $this->manager->add($middleware);
        
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        $handler = function($job, $data) {
            return 'processed';
        };
        
        $result = $this->manager->process($job, $data, $handler);
        
        $this->assertEquals('processed', $result);
        $this->assertTrue($middleware->beforeCalled);
        $this->assertTrue($middleware->afterCalled);
    }

    public function testProcessWithMultipleMiddleware(): void
    {
        $middleware1 = new TestMiddleware();
        $middleware2 = new TestMiddleware();
        
        $this->manager->add($middleware1);
        $this->manager->add($middleware2);
        
        $job = $this->createMockJob();
        $data = ['test' => 'data'];
        $handler = function($job, $data) {
            return 'processed';
        };
        
        $result = $this->manager->process($job, $data, $handler);
        
        $this->assertEquals('processed', $result);
        $this->assertTrue($middleware1->beforeCalled);
        $this->assertTrue($middleware1->afterCalled);
        $this->assertTrue($middleware2->beforeCalled);
        $this->assertTrue($middleware2->afterCalled);
    }

    private function createMockJob(): Job
    {
        return $this->createMock(Job::class);
    }
}

class TestMiddleware implements MiddlewareInterface
{
    public bool $beforeCalled = false;
    public bool $afterCalled = false;

    public function handle(Job $job, array $data, callable $next): mixed
    {
        $this->beforeCalled = true;
        
        $result = $next($job, $data);
        
        $this->afterCalled = true;
        
        return $result;
    }
}

