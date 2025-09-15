<?php

namespace Sunmking\Think8Queue\Contracts;

interface JobBuilderInterface
{
    /**
     * 设置任务类
     */
    public function job(string $job): self;

    /**
     * 设置任务数据
     */
    public function data(array $data): self;

    /**
     * 设置队列名称
     */
    public function queue(?string $queue): self;

    /**
     * 设置延迟时间
     */
    public function delay(int $seconds): self;

    /**
     * 设置最大重试次数
     */
    public function attempts(int $attempts): self;

    /**
     * 设置超时时间
     */
    public function timeout(int $seconds): self;

    /**
     * 设置任务优先级
     */
    public function priority(int $priority): self;

    /**
     * 推送任务
     */
    public function dispatch(): mixed;
}
