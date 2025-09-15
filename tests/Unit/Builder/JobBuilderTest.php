<?php

namespace Sunmking\Think8Queue\Tests\Unit\Builder;

use Sunmking\Think8Queue\Builder\JobBuilder;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Manager\QueueManager;
use Sunmking\Think8Queue\Tests\TestCase;

class JobBuilderTest extends TestCase
{
    private JobBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new JobBuilder($this->queueManager, $this->config);
    }

    public function testJobMethod(): void
    {
        $result = $this->builder->job('TestJob');
        
        $this->assertSame($this->builder, $result);
    }

    public function testDataMethod(): void
    {
        $data = ['key' => 'value'];
        $result = $this->builder->data($data);
        
        $this->assertSame($this->builder, $result);
    }

    public function testQueueMethod(): void
    {
        $result = $this->builder->queue('test_queue');
        
        $this->assertSame($this->builder, $result);
    }

    public function testDelayMethod(): void
    {
        $result = $this->builder->delay(60);
        
        $this->assertSame($this->builder, $result);
    }

    public function testAttemptsMethod(): void
    {
        $result = $this->builder->attempts(5);
        
        $this->assertSame($this->builder, $result);
    }

    public function testTimeoutMethod(): void
    {
        $result = $this->builder->timeout(120);
        
        $this->assertSame($this->builder, $result);
    }

    public function testPriorityMethod(): void
    {
        $result = $this->builder->priority(10);
        
        $this->assertSame($this->builder, $result);
    }

    public function testFluentInterface(): void
    {
        $result = $this->builder
            ->job('TestJob')
            ->data(['test' => 'data'])
            ->queue('test_queue')
            ->delay(60)
            ->attempts(5)
            ->timeout(120)
            ->priority(10);
        
        $this->assertSame($this->builder, $result);
    }
}

