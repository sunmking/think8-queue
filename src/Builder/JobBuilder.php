<?php

namespace Sunmking\Think8Queue\Builder;

use Sunmking\Think8Queue\Contracts\JobBuilderInterface;
use Sunmking\Think8Queue\Contracts\QueueInterface;
use Sunmking\Think8Queue\Config\QueueConfig;

class JobBuilder implements JobBuilderInterface
{
    private string $job = '';
    private array $data = [];
    private ?string $queue = null;
    private int $delay = 0;
    private int $attempts = 0;
    private int $timeout = 0;
    private int $priority = 0;

    public function __construct(
        private QueueInterface $queueManager,
        private QueueConfig $config
    ) {}

    public function job(string $job): self
    {
        $this->job = $job;
        return $this;
    }

    public function data(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function queue(?string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function delay(int $seconds): self
    {
        $this->delay = $seconds;
        return $this;
    }

    public function attempts(int $attempts): self
    {
        $this->attempts = $attempts;
        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;
        return $this;
    }

    public function priority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function dispatch(): mixed
    {
        $queue = $this->queue ?? $this->config->get('default_queue');
        $jobData = $this->buildJobData();

        if ($this->delay > 0) {
            return $this->queueManager->later($this->delay, $this->job, $jobData, $queue);
        }

        return $this->queueManager->push($this->job, $jobData, $queue);
    }

    private function buildJobData(): array
    {
        return array_merge($this->data, [
            'attempts' => $this->attempts ?: $this->config->get('default_attempts'),
            'timeout' => $this->timeout ?: $this->config->get('default_timeout'),
            'priority' => $this->priority ?: $this->config->get('default_priority'),
        ]);
    }
}
