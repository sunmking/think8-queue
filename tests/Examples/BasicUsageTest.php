<?php

namespace Sunmking\Think8Queue\Tests\Examples;

use Sunmking\Think8Queue\Facades\Queue;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Tests\Helpers\TestJob;
use Sunmking\Think8Queue\Tests\TestCase;

class BasicUsageTest extends TestCase
{
    public function testBasicJobCreation(): void
    {
        // 重置测试状态
        TestJob::reset();
        
        // 创建任务
        $builder = Queue::job(TestJob::class);
        
        $this->assertInstanceOf(\Sunmking\Think8Queue\Builder\JobBuilder::class, $builder);
    }

    public function testJobWithData(): void
    {
        TestJob::reset();
        
        $builder = Queue::job(TestJob::class)
            ->data(['id' => 123, 'name' => 'test']);
        
        $this->assertInstanceOf(\Sunmking\Think8Queue\Builder\JobBuilder::class, $builder);
    }

    public function testJobWithOptions(): void
    {
        TestJob::reset();
        
        $builder = Queue::job(TestJob::class)
            ->data(['id' => 123])
            ->delay(60)
            ->attempts(5)
            ->timeout(120)
            ->priority(10)
            ->queue('test_queue');
        
        $this->assertInstanceOf(\Sunmking\Think8Queue\Builder\JobBuilder::class, $builder);
    }

    public function testConfigurationAccess(): void
    {
        // 确保配置初始化为 'test'
        $config = new QueueConfig(['queue' => 'test']);
        
        $this->assertInstanceOf(QueueConfig::class, $config);
        $this->assertEquals('test', $config->get('default_queue'));
    }

    public function testCustomConfiguration(): void
    {
        $config = new QueueConfig(['queue' => 'custom']);
        
        $this->assertEquals('custom', $config->get('queue'));
        $this->assertEquals(5, $config->get('default_attempts'));
    }
}
