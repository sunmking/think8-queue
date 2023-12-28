<?php

namespace Sunmking\Think8Queue;

use RuntimeException;
use Sunmking\Think8Queue\traits\ErrTrait;
use think\facade\Config;
use think\facade\Queue as QueueThink;

class Queue
{
    use ErrTrait;

    /**
     * 任务执行
     *
     * @var string
     */
    protected string $do = 'doJob';

    /**
     * 默认任务执行方法名
     *
     * @var string
     */
    protected string $defaultDo;

    /**
     * 队列类名
     * @var string
     */
    protected string $job;

    /**
     * 错误次数
     *
     * @var int
     */
    protected int $errorCount = 3;

    /**
     * 数据
     *
     * @var array|string
     */
    protected string|array $data;

    /**
     * 任务名
     *
     * @var null
     */
    protected $queueName = null;

    /**
     * 延迟执行秒数
     *
     * @var int
     */
    protected int $secs = 0;

    /**
     * 重新发布时延迟执行秒数
     *
     * @var int
     */
    protected int $delay = 0;

    /**
     * 记录日志
     *
     * @var string|callable|array
     */
    protected $log;

    /**
     * @var array
     */
    protected array $rules = ['do', 'data', 'errorCount', 'job', 'secs', 'delay', 'queueName', 'log'];

    /**
     * @var static
     */
    protected static Queue $instance;

    /**
     * Queue constructor.
     */
    protected function __construct()
    {
        $this->defaultDo = $this->do;
    }

    /**
     * @return static
     */
    public static function instance(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * @param string $queueName
     * @return $this
     */
    public function setQueueName(string $queueName): static
    {
        $this->queueName = $queueName;
        return $this;
    }

    /**
     * 放入消息队列
     *
     * @param array|null $data
     * @return mixed
     */
    public function push(?array $data = null): mixed
    {
        if (!$this->job) {
            return $this->setError('需要执行的队列类必须存在');
        }
        $res = QueueThink::{$this->action()}(...$this->getValues($data));
        if (!$res) {
            $res = QueueThink::{$this->action()}(...$this->getValues($data));
            if (!$res) {
                return $this->setError('加入队列失败');
            }
        }
        $this->clean();

        return $res;
    }

    /**
     * 清除数据
     */
    public function clean(): void
    {
        $this->secs = 0;
        $this->delay = 0;
        $this->data = [];
        $this->log = null;
        $this->queueName = null;
        $this->errorCount = 3;
        $this->do = $this->defaultDo;
    }

    /**
     * 获取任务方式
     */
    protected function action(): string
    {
        return $this->secs ? 'later' : 'push';
    }

    /**
     * 获取参数
     */
    protected function getValues($data): array
    {
        $jobData['data'] = $data ?: $this->data;
        $jobData['do'] = $this->do;
        $jobData['errorCount'] = $this->errorCount;
        $jobData['log'] = $this->log;
        $jobData['delay'] = $this->delay;
        if ($this->do != $this->defaultDo) {
            $this->job .= '@'.Config::get('queue.prefix', 'qn_').$this->do;
        }
        if ($this->secs) {
            return [$this->secs, $this->job, $jobData, $this->queueName];
        } else {
            return [$this->job, $jobData, $this->queueName];
        }
    }

    /**
     * @return $this
     */
    public function __call($name, $arguments): static
    {
        if (in_array($name, $this->rules)) {
            if ($name === 'data') {
                $this->{$name} = $arguments;
            } else {
                $this->{$name} = $arguments[0] ?? null;
            }

            return $this;
        } else {
            throw new RuntimeException('Method does not exist'.__CLASS__.'->'.$name.'()');
        }
    }
}
