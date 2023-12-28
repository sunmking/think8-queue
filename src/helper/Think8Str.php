<?php

namespace Sunmking\Think8Vite\helper;

class Think8Str
{
    public static function finish($value, $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }
}