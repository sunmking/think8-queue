<?php
namespace Sunmking\Think8Queue\Tests\Integration\Middleware;

class LogStub
{
    public static array $messages = [];

    public static function info($message, $context = [])
    {
        self::$messages[] = $message;
    }

    public static function error($message, $context = [])
    {
        self::$messages[] = $message;
    }

    public static function reset()
    {
        self::$messages = [];
    }
}
