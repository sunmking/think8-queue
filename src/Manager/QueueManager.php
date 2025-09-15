<?php

namespace Sunmking\Think8Queue\Manager;

use Sunmking\Think8Queue\Builder\JobBuilder;
use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Contracts\JobBuilderInterface;
use Sunmking\Think8Queue\Contracts\QueueInterface;
use think\facade\Queue as ThinkQueue;

class QueueManager implements QueueInterface
{
    private static ?QueueManager $instance = null;

    public function __construct(
        private QueueConfig $config
    ) {}

    public static function instance(?QueueConfig $config = null): self
    {
        if (self::$instance === null || $config !== null) {
            self::$instance = new self($config ?? new QueueConfig());
        }
        return self::$instance;
    }

    public function push(string $job, array $data = [], ?string $queue = null): mixed
    {
        $queue = $queue ?? $this->config->get('default_queue');
        return ThinkQueue::push($job, $data, $queue);
    }

    public function later(int $delay, string $job, array $data = [], ?string $queue = null): mixed
    {
        $queue = $queue ?? $this->config->get('default_queue');
        return ThinkQueue::later($delay, $job, $data, $queue);
    }

    public function bulk(array $jobs, array $data = [], ?string $queue = null): mixed
    {
        $queue = $queue ?? $this->config->get('default_queue');
        return ThinkQueue::bulk($jobs, $data, $queue);
    }

    public function job(string $job): JobBuilderInterface
    {
        return new JobBuilder($this, $this->config);
    }

    public function config(): QueueConfig
    {
        return $this->config;
    }

    public function getConfig()
    {
        return $this->config;
    }
}
