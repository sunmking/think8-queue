<?php

namespace Sunmking\Think8Queue\Contracts;

use think\queue\Job;

interface JobInterface
{
    /**
     * 执行队列任务
     */
    public function handle(Job $job, array $data): void;

    /**
     * 任务失败时的处理
     */
    public function failed(array $data, \Throwable $exception): void;
}
