<?php

namespace Sunmking\Think8Queue\Jobs;

use Sunmking\Think8Queue\Middleware\LoggingMiddleware;
use Sunmking\Think8Queue\Middleware\RetryMiddleware;
use think\queue\Job;

class ExampleJob extends AbstractJob
{
    public function __construct()
    {
        parent::__construct();
        
        // 添加中间件
        $this->middleware(LoggingMiddleware::class)
             ->middleware(RetryMiddleware::class);
    }

    protected function execute(Job $job, array $data): void
    {
        // 示例任务逻辑
        $id = $data['id'] ?? null;
        
        if (!$id) {
            throw new \InvalidArgumentException('ID is required');
        }
        
        // 模拟一些工作
        sleep(1);
        
        // 这里可以添加你的业务逻辑
        echo "Processing job with ID: {$id}\n";
    }
}
