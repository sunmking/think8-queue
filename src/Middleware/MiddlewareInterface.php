<?php

namespace Sunmking\Think8Queue\Middleware;

use think\queue\Job;

interface MiddlewareInterface
{
    /**
     * 处理任务
     */
    public function handle(Job $job, array $data, callable $next): mixed;
}
