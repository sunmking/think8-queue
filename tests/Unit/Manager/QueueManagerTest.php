<?php

namespace Sunmking\Think8Queue\Tests\Unit\Manager;

use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Manager\QueueManager;
use Sunmking\Think8Queue\Tests\TestCase;

class QueueManagerTest extends TestCase
{
    public function testSingletonInstance(): void
    {
        $instance1 = QueueManager::instance();
        $instance2 = QueueManager::instance();
        
        $this->assertSame($instance1, $instance2);
    }

    public function testInstanceWithConfig(): void
    {
        $config = new QueueConfig(['default_queue' => 'test']);
        $manager = QueueManager::instance($config);
        
        $this->assertInstanceOf(QueueManager::class, $manager);
        $this->assertEquals('test', $manager->config()->get('default_queue'));
    }

    public function testJobBuilderCreation(): void
    {
        $builder = $this->queueManager->job('TestJob');
        
        $this->assertInstanceOf(\Sunmking\Think8Queue\Builder\JobBuilder::class, $builder);
    }

    public function testConfigAccess(): void
    {
        $config = $this->queueManager->config();
        
        $this->assertInstanceOf(QueueConfig::class, $config);
        $this->assertEquals('test', $config->get('default_queue'));
    }
}

