# think8-queue

适用于 thinkphp6 & thinkphp8

> 基于官方 think-queue 封装的类库, 使用便捷
> 配置方法和使用方法与 think-queue 一致

## 特性

- 便捷
- 延时队列、延时任务使用更加直观


## 安装
> composer require sunmking/think8-queue

## 使用

### 标准使用 (一)
> 继承 BaseJob 类
> 实现 doJob 方法
> 调用 push 方法

```php
// 引入队列类
use Sunmking\ThinkQueue\basic\BaseJob;
use think\facade\Log;
class TestJob extends BaseJob
{
    public function doJob($id): bool
    {
        try {
            $res = $this->dealJob($id);
        } catch (Exception $e) {
            $res = false;
            Log::error("关闭出错 order_id:$id:".$e->getMessage());
        }

        return $res;
    }
    public function dealJob($id)
    {
        // 业务逻辑
        return true;
    }
}
```

> 在需要 加入队列的地方

```php
// 引入队列类
use Sunmking\ThinkQueue\Queue;
use app\conmmon\jobs\TestJob;

Queue::instance()->job(TestJob::class)
                        ->errorCount(3)
                        ->delay(10) // 失败重试间隔时间
                        ->secs(10) // 延时执行时间
                        ->data($id)
                        ->push();
```

### trait 使用 (二)
> 继承 BaseJob 类
> 使用 trait QueueTrait
> 实现 doJob 方法
> 调用 dispatch 方法

```php
// 引入队列类
use Sunmking\ThinkQueue\traits\QueueTrait;
use Sunmking\ThinkQueue\basic\BaseJob;
use think\facade\Log;

class TestJob extends BaseJob
{
    use QueueTrait;
    public function doJob($id): bool
    {
        try {
            $res = $this->dealJob($id);
        } catch (Exception $e) {
            $res = false;
            Log::error("关闭出错 order_id:$id:".$e->getMessage());
        }

        return $res;
    }
    public function dealJob($id)
    {
        // 业务逻辑
        return true;
    }
}
```

> 在需要 加入队列的地方

```php
// 引入队列类
use app\conmmon\jobs\TestJob;
// 调用方法 方法名  入参  延时时间  失败重试次数  失败重试间隔时间
TestJob::dispatch('doJob', [$id], 240, 4, 30);
```

## 最后

> 启动消费队列 最好使用 supervisor 守护进程

```php
php think queue:listen --memory 8 --queue default
```
