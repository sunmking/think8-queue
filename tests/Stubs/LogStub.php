<?php
namespace Sunmking\Think8Queue\Tests\Stubs;

class LogStub
{
    public static function info($message, $context = [])
    {
        // 可记录到静态变量或直接忽略
    }
    public static function error($message, $context = [])
    {
        // 可记录到静态变量或直接忽略
    }
}
