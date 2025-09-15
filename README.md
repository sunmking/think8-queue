# Think8-Queue

> 基于现代设计模式重构的 ThinkPHP 8 队列库

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://www.php.net/)
[![ThinkPHP](https://img.shields.io/badge/thinkphp-8.0+-green.svg)](https://www.thinkphp.cn/)
[![License](https://img.shields.io/badge/license-MIT-red.svg)](LICENSE)

## ✨ 特性

- 🏗️ **Builder模式**：流畅的API设计，支持链式调用
- 🎯 **中间件支持**：可插拔的中间件系统，灵活扩展
- 📡 **事件系统**：完整的事件监听机制，支持任务生命周期
- ⚙️ **配置管理**：灵活的配置系统，支持动态配置
- 🔧 **类型安全**：完整的类型声明，减少运行时错误
- 🚀 **高性能**：优化的执行流程，支持批量任务
- 🔄 **向后兼容**：保持与旧版本的兼容性

## 📦 安装

```bash
composer require sunmking/think8-queue
```

## 🚀 快速开始

### 1. 创建任务类

```php
<?php

namespace App\Jobs;

use Sunmking\Think8Queue\Jobs\AbstractJob;
use Sunmking\Think8Queue\Middleware\LoggingMiddleware;
use think\queue\Job;

class SendEmailJob extends AbstractJob
{
    public function __construct()
    {
        parent::__construct();
        
        // 添加中间件
        $this->middleware(LoggingMiddleware::class);
    }

    protected function execute(Job $job, array $data): void
    {
        $email = $data['email'] ?? '';
        $subject = $data['subject'] ?? '';
        $content = $data['content'] ?? '';
        
        // 发送邮件逻辑
        echo "Sending email to: {$email}\n";
        echo "Subject: {$subject}\n";
        echo "Content: {$content}\n";
    }
}
```

### 2. 使用队列

#### 方式一：使用Builder模式（推荐）

```php
use Sunmking\Think8Queue\Facades\Queue;

// 基本使用
Queue::job(SendEmailJob::class)
    ->data([
        'email' => 'user@example.com',
        'subject' => 'Welcome',
        'content' => 'Welcome to our service!'
    ])
    ->dispatch();

// 高级配置
Queue::job(SendEmailJob::class)
    ->data(['email' => 'user@example.com'])
    ->delay(60)        // 60秒后执行
    ->attempts(5)      // 最多重试5次
    ->timeout(120)     // 超时时间120秒
    ->priority(10)     // 优先级
    ->queue('emails')  // 指定队列
    ->dispatch();
```

#### 方式二：直接使用Manager

```php
use Sunmking\Think8Queue\Manager\QueueManager;

$queue = QueueManager::instance();

// 推送任务
$queue->push(SendEmailJob::class, [
    'email' => 'user@example.com',
    'subject' => 'Test',
    'content' => 'Test content'
]);

// 延迟推送
$queue->later(300, SendEmailJob::class, [
    'email' => 'user@example.com'
]);
```

## ⚙️ 配置

```php
use Sunmking\Think8Queue\Facades\Queue;

// 设置配置
Queue::config([
    'default_queue' => 'default',
    'default_attempts' => 3,
    'default_timeout' => 60,
    'default_priority' => 0,
    'prefix' => 'qn_',
    'retry_after' => 90,
]);
```

## 🎯 中间件系统

### 内置中间件

```php
use Sunmking\Think8Queue\Middleware\LoggingMiddleware;
use Sunmking\Think8Queue\Middleware\RetryMiddleware;

class MyJob extends AbstractJob
{
    public function __construct()
    {
        parent::__construct();
        
        // 添加日志中间件
        $this->middleware(LoggingMiddleware::class);
        
        // 添加重试中间件
        $this->middleware(RetryMiddleware::class);
    }
}
```

### 自定义中间件

```php
<?php

namespace App\Middleware;

use Sunmking\Think8Queue\Middleware\MiddlewareInterface;
use think\queue\Job;

class CustomMiddleware implements MiddlewareInterface
{
    public function handle(Job $job, array $data, callable $next): mixed
    {
        // 前置处理
        echo "Before job execution\n";
        
        $result = $next($job, $data);
        
        // 后置处理
        echo "After job execution\n";
        
        return $result;
    }
}
```

## 📡 事件系统

```php
use Sunmking\Think8Queue\Events\JobEvent;

class SendEmailJob extends AbstractJob
{
    public function __construct()
    {
        parent::__construct();
        
        // 监听任务开始事件
        $this->on(JobEvent\JobProcessing::class, function($job, $data) {
            echo "Job started: " . get_class($job) . "\n";
        });
        
        // 监听任务完成事件
        $this->on(JobEvent\JobProcessed::class, function($job, $data) {
            echo "Job completed: " . get_class($job) . "\n";
        });
        
        // 监听任务失败事件
        $this->on(JobEvent\JobFailed::class, function($job, $data, $exception) {
            echo "Job failed: " . $exception->getMessage() . "\n";
        });
    }
}
```

## 📦 批量任务

```php
use Sunmking\Think8Queue\Manager\QueueManager;

$queue = QueueManager::instance();

// 批量推送
$jobs = [
    SendEmailJob::class,
    ProcessOrderJob::class,
    GenerateReportJob::class
];

$queue->bulk($jobs, [
    'batch_id' => 'batch_001'
]);
```

## 🔄 迁移指南

### 从旧版本迁移

#### 1. 任务类迁移

**旧版本:**
```php
use Sunmking\Think8Queue\basic\BaseJob;

class OldJob extends BaseJob
{
    public function doJob($id): bool
    {
        // 业务逻辑
        return true;
    }
}
```

**新版本:**
```php
use Sunmking\Think8Queue\Jobs\AbstractJob;
use think\queue\Job;

class NewJob extends AbstractJob
{
    protected function execute(Job $job, array $data): void
    {
        $id = $data['id'] ?? $data[0] ?? null;
        // 业务逻辑
    }
}
```

#### 2. 队列调用迁移

**旧版本:**
```php
use Sunmking\Think8Queue\Queue;

Queue::instance()->job(TestJob::class)
    ->errorCount(3)
    ->delay(10)
    ->secs(10)
    ->data($id)
    ->push();
```

**新版本:**
```php
use Sunmking\Think8Queue\Facades\Queue;

Queue::job(TestJob::class)
    ->data(['id' => $id])
    ->attempts(3)
    ->delay(10)
    ->timeout(120)
    ->dispatch();
```

## 🏗️ 架构设计

### 核心组件

- **QueueManager**: 队列管理器，负责任务调度
- **JobBuilder**: 任务构建器，提供流畅的API
- **AbstractJob**: 抽象任务基类
- **MiddlewareManager**: 中间件管理器
- **EventDispatcher**: 事件分发器
- **QueueConfig**: 配置管理器

### 设计模式

- **Builder模式**: 用于构建复杂的任务配置
- **策略模式**: 用于不同的任务处理策略
- **观察者模式**: 用于事件系统
- **责任链模式**: 用于中间件系统

## 📁 目录结构

```
src/
├── Contracts/          # 接口定义
│   ├── JobInterface.php
│   ├── QueueInterface.php
│   └── JobBuilderInterface.php
├── Config/            # 配置管理
│   └── QueueConfig.php
├── Events/            # 事件系统
│   ├── JobEvent.php
│   └── EventDispatcher.php
├── Middleware/        # 中间件系统
│   ├── MiddlewareInterface.php
│   ├── MiddlewareManager.php
│   ├── LoggingMiddleware.php
│   └── RetryMiddleware.php
├── Jobs/              # 任务类
│   ├── AbstractJob.php
│   └── ExampleJob.php
├── Builder/           # 构建器
│   └── JobBuilder.php
├── Manager/           # 管理器
│   └── QueueManager.php
└── Facades/           # 门面类
    └── Queue.php
```

## 🚀 启动队列

```bash
# 启动消费队列（推荐使用 supervisor 守护进程）
php think queue:listen --memory 8 --queue default

# 指定队列
php think queue:listen --queue emails

# 指定进程数
php think queue:listen --queue default --tries 3 --timeout 60
```

## 📊 性能优化

- 使用对象池减少内存分配
- 中间件按需加载
- 事件系统异步处理
- 配置缓存机制
- 批量任务处理

## 🧪 测试

```bash
# 运行测试
composer test

# 代码覆盖率
composer test-coverage
```

## 📝 更新日志

### v2.0.0 (重构版)
- 🎉 完全重构，采用现代设计模式
- ✨ 新增Builder模式API
- 🎯 新增中间件系统
- 📡 新增事件系统
- ⚙️ 新增配置管理
- 🔧 增强类型安全
- 🚀 性能优化

### v1.x (旧版本)
- 基础队列功能
- 简单的任务处理
- 基本的错误处理

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📄 许可证

MIT License

## 📞 联系方式

- 作者：sunmking
- 邮箱：1042080686@qq.com