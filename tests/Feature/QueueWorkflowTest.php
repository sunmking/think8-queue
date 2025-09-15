<?php

namespace Sunmking\Think8Queue\Tests\Feature;

use Sunmking\Think8Queue\Builder\JobBuilder;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Manager\QueueManager;
use Sunmking\Think8Queue\Tests\TestCase;

class QueueWorkflowTest extends TestCase
{
    public function testCompleteWorkflow(): void
    {
        // 测试完整的队列工作流程
        $builder = $this->queueManager->job('TestJob');
        
        $this->assertInstanceOf(JobBuilder::class, $builder);
        
        // 测试链式调用
        $result = $builder
            ->data(['test' => 'data'])
            ->queue('test_queue')
            ->delay(60)
            ->attempts(5)
            ->timeout(120)
            ->priority(10);
        
        $this->assertSame($builder, $result);
    }

    public function testJobBuilderDataBuilding(): void
    {
        $builder = new JobBuilder($this->queueManager, $this->config);
        
        // 使用反射测试私有方法
        $reflection = new \ReflectionClass($builder);
        $method = $reflection->getMethod('buildJobData');
        $method->setAccessible(true);
        
        $builder->data(['test' => 'data'])
                ->attempts(5)
                ->timeout(120)
                ->priority(10);
        
        $jobData = $method->invoke($builder);
        
        $data = isset($jobData['data']) ? $jobData['data'] : [];
        
        $this->assertEquals(['test' => 'data'], $data);
        $this->assertEquals(5, $jobData['attempts']);
        $this->assertEquals(120, $jobData['timeout']);
        $this->assertEquals(10, $jobData['priority']);
    }

    public function testConfigurationIntegration(): void
    {
        $config = new QueueConfig([
            'default_queue' => 'custom',
            'default_attempts' => 5,
            'default_timeout' => 120,
        ]);
        
        $manager = new QueueManager($config);
        $builder = $manager->job('TestJob');
        
        $reflection = new \ReflectionClass($builder);
        $method = $reflection->getMethod('buildJobData');
        $method->setAccessible(true);
        
        $jobData = $method->invoke($builder);
        
        $this->assertEquals(5, $jobData['attempts']);
        $this->assertEquals(120, $jobData['timeout']);
        $this->assertEquals(0, $jobData['priority']); // 默认值
    }
}

