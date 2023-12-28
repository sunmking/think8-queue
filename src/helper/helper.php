<?php

use Sunmking\Think8Vite\collections\Think8Collection;

if (!function_exists('think8_collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param mixed $value
     * @return Think8Collection
     */
    function think8_collect($value = null)
    {
        return new Think8Collection($value);
    }
}

if (! function_exists('think8_asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool|null  $secure
     * @return string
     */
    function think8_asset($path, $secure = null): string
    {
        return app('url')->asset($path, $secure);
    }
}