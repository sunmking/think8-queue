# 测试文档

## 测试结构

```
tests/
├── Unit/                    # 单元测试
│   ├── Config/
│   │   └── QueueConfigTest.php
│   ├── Manager/
│   │   └── QueueManagerTest.php
│   ├── Builder/
│   │   └── JobBuilderTest.php
│   ├── Events/
│   │   └── EventDispatcherTest.php
│   └── Middleware/
│       └── MiddlewareManagerTest.php
├── Integration/             # 集成测试
│   ├── Jobs/
│   │   └── AbstractJobTest.php
│   └── Middleware/
│       ├── LoggingMiddlewareTest.php
│       └── RetryMiddlewareTest.php
├── Feature/                 # 功能测试
│   ├── QueueWorkflowTest.php
│   └── FacadeTest.php
├── Helpers/                 # 测试辅助类
│   ├── MockJob.php
│   └── TestJob.php
├── TestCase.php            # 测试基类
└── bootstrap.php           # 测试引导文件
```

## 运行测试

### 使用Composer脚本

```bash
# 运行所有测试
composer test

# 运行单元测试
composer test-unit

# 运行集成测试
composer test-integration

# 运行功能测试
composer test-feature

# 生成覆盖率报告
composer test-coverage
```

### 使用PHPUnit直接运行

```bash
# 运行所有测试
./vendor/bin/phpunit

# 运行特定测试套件
./vendor/bin/phpunit --testsuite Unit
./vendor/bin/phpunit --testsuite Integration
./vendor/bin/phpunit --testsuite Feature

# 运行特定测试文件
./vendor/bin/phpunit tests/Unit/Config/QueueConfigTest.php

# 运行特定测试方法
./vendor/bin/phpunit --filter testDefaultConfiguration
```

### 使用脚本运行

**Windows:**
```cmd
run-tests.bat
```

**Linux/Mac:**
```bash
./run-tests.sh
```

## 测试覆盖

### 单元测试覆盖
- ✅ QueueConfig - 配置管理
- ✅ QueueManager - 队列管理器
- ✅ JobBuilder - 任务构建器
- ✅ EventDispatcher - 事件分发器
- ✅ MiddlewareManager - 中间件管理器

### 集成测试覆盖
- ✅ AbstractJob - 抽象任务基类
- ✅ LoggingMiddleware - 日志中间件
- ✅ RetryMiddleware - 重试中间件
- ✅ 事件系统集成
- ✅ 中间件系统集成

### 功能测试覆盖
- ✅ 完整队列工作流程
- ✅ Facade门面类
- ✅ 配置集成
- ✅ 链式调用

## 测试辅助类

### MockJob
模拟ThinkPHP的Job接口，用于测试：

```php
use Sunmking\Think8Queue\Tests\Helpers\MockJob;

$job = new MockJob(3); // 3次尝试
$job->delete();
$job->release(60);
```

### TestJob
用于测试的示例任务类：

```php
use Sunmking\Think8Queue\Tests\Helpers\TestJob;

// 重置状态
TestJob::reset();

// 设置失败
TestJob::$shouldFail = true;
TestJob::$failureMessage = 'Custom error';
```

## 测试最佳实践

### 1. 测试命名
- 测试方法使用 `test` 前缀或 `@test` 注解
- 描述性命名：`testShouldReturnTrueWhenValidDataProvided`

### 2. 测试结构
- **Arrange**: 准备测试数据
- **Act**: 执行被测试的方法
- **Assert**: 验证结果

### 3. 断言使用
```php
// 基本断言
$this->assertTrue($condition);
$this->assertFalse($condition);
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);

// 异常断言
$this->expectException(\InvalidArgumentException::class);
$this->expectExceptionMessage('Invalid data');

// 数组断言
$this->assertArrayHasKey('key', $array);
$this->assertContains('value', $array);
```

### 4. Mock对象
```php
// 创建Mock对象
$mock = $this->createMock(SomeClass::class);

// 设置方法返回值
$mock->method('someMethod')->willReturn('value');

// 设置方法调用次数
$mock->expects($this->once())->method('someMethod');
```

## CI/CD集成

### GitHub Actions
项目配置了GitHub Actions工作流，支持：
- 多PHP版本测试 (8.0, 8.1, 8.2, 8.3)
- 自动依赖安装
- 测试覆盖率报告
- Codecov集成

### 本地开发
```bash
# 安装依赖
composer install

# 运行测试
composer test

# 查看覆盖率
composer test-coverage
# 打开 coverage/index.html 查看详细报告
```

## 测试数据

### 配置测试数据
```php
protected function setUp(): void
{
    parent::setUp();
    
    $this->config = new QueueConfig([
        'default_queue' => 'test',
        'default_attempts' => 3,
        'default_timeout' => 60,
    ]);
}
```

### 模拟ThinkPHP环境
测试引导文件 `bootstrap.php` 提供了：
- Mock Queue facade
- Mock Log facade  
- Mock Config facade
- 测试环境配置

## 故障排除

### 常见问题

1. **类不存在错误**
   - 确保运行了 `composer install --dev`
   - 检查autoload配置

2. **Mock对象问题**
   - 确保使用了正确的Mock方法
   - 检查方法签名匹配

3. **测试环境问题**
   - 确保bootstrap.php正确加载
   - 检查环境变量设置

### 调试技巧

```php
// 输出调试信息
var_dump($variable);

// 使用断言消息
$this->assertEquals($expected, $actual, 'Custom error message');

// 检查Mock调用
$mock->expects($this->exactly(2))->method('someMethod');
```
