<?php

namespace Sunmking\Think8Queue\Facades;

use Sunmking\Think8Queue\Builder\JobBuilder;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Manager\QueueManager;

class Queue
{
    public static function __callStatic(string $method, array $arguments): mixed
    {
        $instance = QueueManager::instance();
        
        if ($method === 'job') {
            return $instance->job($arguments[0] ?? '');
        }
        
        return $instance->{$method}(...$arguments);
    }

    public static function config(?array $config = null): QueueConfig
    {
        if ($config !== null) {
            return QueueManager::instance(new QueueConfig($config))->config();
        }
        
        return QueueManager::instance()->config();
    }
}
