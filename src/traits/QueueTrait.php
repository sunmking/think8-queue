<?php

namespace Sunmking\Think8Queue\traits;

use Sunmking\Think8Queue\Queue;

trait QueueTrait
{
    /**
     * 列名
     * @return null
     */
    protected static function queueName()
    {
        return null;
    }

    /**
     * 加入队列
     * @param $action
     * @param array $data
     * @param int $delay 重新发布间隔时间
     * @param int $errorCount 重试次数
     * @param int $secs 延时加入队列时间
     * @param string|null $queueName
     * @return mixed
     */
    public static function dispatch($action, array $data = [], int $delay = 0, int $errorCount = 0, int $secs = 0, string $queueName = null): mixed
    {
        $queue = Queue::instance()->job(__CLASS__);
        if (is_array($action)) {
            $queue->data(...$action);
        } elseif (is_string($action)) {
            $queue->do($action)->data(...$data);
        }
        if($secs > 0) {
            $queue->secs($secs);
        }
        if($delay > 0) {
            $queue->delay($delay);
        }
        if($errorCount > 0) {
            $queue->errorCount($errorCount);
        }
        if ($queueName) {
            $queue->setQueueName($queueName);
        } elseif (static::queueName()) {
            $queue->setQueueName(static::queueName());
        }

        return $queue->push();
    }
}