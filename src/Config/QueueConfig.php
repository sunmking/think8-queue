<?php

namespace Sunmking\Think8Queue\Config;

class QueueConfig
{
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'default_queue' => 'default',
            'default_attempts' => 3,
            'default_timeout' => 60,
            'default_priority' => 0,
            'prefix' => 'qn_',
            'retry_after' => 90,
        ], $config);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;
        return $this;
    }

    public function all(): array
    {
        return $this->config;
    }
}
