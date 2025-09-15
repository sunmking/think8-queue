<?php

// 测试引导文件
require_once __DIR__ . '/../vendor/autoload.php';

// 设置测试环境
if (!defined('APP_ENV')) {
    define('APP_ENV', 'testing');
}

// 模拟ThinkPHP环境
if (!class_exists('think\facade\Queue')) {
    // 创建模拟的Queue facade
    class MockQueue
    {
        public static function push(...$args)
        {
            return 'mock-job-id';
        }

        public static function later(...$args)
        {
            return 'mock-delayed-job-id';
        }

        public static function bulk(...$args)
        {
            return ['mock-bulk-job-1', 'mock-bulk-job-2'];
        }
    }

    if (!class_exists('think\facade\Queue')) {
        class_alias('MockQueue', 'think\facade\Queue');
    }
}

// 模拟ThinkPHP Log facade
if (!class_exists('think\facade\Log')) {
    class MockLog
    {
        public static function info($message, $context = [])
        {
            echo "[INFO] $message\n";
        }

        public static function error($message, $context = [])
        {
            echo "[ERROR] $message\n";
        }
    }

    class_alias('MockLog', 'think\facade\Log');
}

// 模拟ThinkPHP Config facade
if (!class_exists('think\facade\Config')) {
    class MockConfig
    {
        public static function get($key, $default = null)
        {
            $config = [
                'queue.prefix' => 'qn_',
            ];
            
            return $config[$key] ?? $default;
        }
    }

    class_alias('MockConfig', 'think\facade\Config');
}

