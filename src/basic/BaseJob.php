<?php

namespace Sunmking\Think8Queue\basic;
use Sunmking\Think8Queue\interfaces\JobInterface;
use think\facade\Log;
use think\queue\Job;
use Throwable;

/**
 * 消息队列基类
 * Class BaseJob
 */
class BaseJob implements JobInterface
{
    public function __call($name, $arguments)
    {
        $this->fire(...$arguments);
    }

    public function fire(Job $job, $data): void
    {
        try {
            $action = $data['do'] ?? 'doJob'; //任务名
            $infoData = $data['data'] ?? []; //执行数据
            $errorCount = $data['errorCount'] ?? 0; //最大错误次数
            $delay = $data['delay'] ?? 0; //重新发布时延迟执行秒数
            $log = $data['log'] ?? null;
            if (method_exists($this, $action)) {
                if ($this->{$action}(...$infoData)) {
                    //删除任务
                    $job->delete();
                    //记录日志
                    $this->info($log);
                } else {
                    if ($job->attempts() >= $errorCount && $errorCount) {
                        //删除任务
                        $job->delete();
                        //记录日志
                        $this->info($log);
                    } else {
                        //从新放入队列
                        $job->release($delay);
                    }
                }
            } else {
                $job->delete();
            }
        } catch (Throwable $e) {
            $job->delete();
            Log::error('执行消息队列发生错误,错误原因:'.$e->getMessage());
        }
    }

    /**
     * 打印出成功提示
     */
    protected function info($log): void
    {
        try {
            if (is_callable($log)) {
                print_r($log()."\r\n");
            } elseif (is_string($log) || is_array($log)) {
                print_r($log."\r\n");
            }
        } catch (Throwable $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * 任务失败执行方法
     */
    public function failed($data, $e)
    {
    }
}
