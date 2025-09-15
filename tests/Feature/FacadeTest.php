<?php

namespace Sunmking\Think8Queue\Tests\Feature;

use Sunmking\Think8Queue\Facades\Queue;
use Sunmking\Think8Queue\Tests\TestCase;
use Sunmking\Think8Queue\Config\QueueConfig;

class FacadeTest extends TestCase
{
    public function testQueueFacadeJobMethod(): void
    {
        $builder = Queue::job('TestJob');
        
        $this->assertInstanceOf(\Sunmking\Think8Queue\Builder\JobBuilder::class, $builder);
    }

    public function testQueueFacadeConfigMethod(): void
    {
        $config = Queue::config();
        
        $this->assertInstanceOf(QueueConfig::class, $config);
    }

    public function testQueueFacadeConfigWithArray()
    {
        $config = new QueueConfig(['key' => 'value']);
        $this->assertInstanceOf(QueueConfig::class, $config);
        $this->assertEquals('value', $config->get('test'));
    }
}

