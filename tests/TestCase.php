<?php

namespace Sunmking\Think8Queue\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Manager\QueueManager;

abstract class TestCase extends BaseTestCase
{
    protected QueueManager $queueManager;
    protected QueueConfig $config;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->config = new QueueConfig([
            'default_queue' => 'test',
            'default_attempts' => 3,
            'default_timeout' => 60,
            'default_priority' => 0,
            'prefix' => 'test_',
            'retry_after' => 90,
        ]);
        
        $this->queueManager = new QueueManager($this->config);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}

