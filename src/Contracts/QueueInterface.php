<?php

namespace Sunmking\Think8Queue\Contracts;

interface QueueInterface
{
    /**
     * 推送任务到队列
     */
    public function push(string $job, array $data = [], ?string $queue = null): mixed;

    /**
     * 延迟推送任务到队列
     */
    public function later(int $delay, string $job, array $data = [], ?string $queue = null): mixed;

    /**
     * 批量推送任务
     */
    public function bulk(array $jobs, array $data = [], ?string $queue = null): mixed;
}
