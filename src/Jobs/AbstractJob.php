<?php

namespace Sunmking\Think8Queue\Jobs;

use Sunmking\Think8Queue\Contracts\JobInterface;
use Sunmking\Think8Queue\Events\EventDispatcher;
use Sunmking\Think8Queue\Events\JobEvent;
use Sunmking\Think8Queue\Middleware\MiddlewareManager;
use think\facade\Log;
use think\queue\Job;
use Throwable;

abstract class AbstractJob implements JobInterface
{
    protected EventDispatcher $eventDispatcher;
    protected MiddlewareManager $middlewareManager;

    public function __construct()
    {
        $this->eventDispatcher = new EventDispatcher();
        $this->middlewareManager = new MiddlewareManager();
    }

    public function handle(Job $job, array $data): void
    {
        $this->eventDispatcher->dispatch(JobEvent\JobProcessing::class, $job, $data);

        try {
            $this->middlewareManager->process($job, $data, function (Job $job, array $data) {
                $this->execute($job, $data);
            });

            $this->eventDispatcher->dispatch(JobEvent\JobProcessed::class, $job, $data);
        } catch (Throwable $e) {
            $this->eventDispatcher->dispatch(JobEvent\JobFailed::class, $job, $data, $e);
            throw $e;
        }
    }

    public function failed(array $data, Throwable $exception): void
    {
        Log::error('Job failed: ' . $exception->getMessage(), [
            'data' => $data,
            'exception' => $exception
        ]);
    }

    /**
     * 执行具体的任务逻辑
     */
    abstract protected function execute(Job $job, array $data): void;

    /**
     * 添加事件监听器
     */
    protected function on(string $event, callable $listener): self
    {
        $this->eventDispatcher->listen($event, $listener);
        return $this;
    }

    /**
     * 添加中间件
     */
    protected function middleware(string|MiddlewareInterface $middleware): self
    {
        $this->middlewareManager->add($middleware);
        return $this;
    }
}
