<?php

namespace Sunmking\Think8Vite\facade;

use think\Facade;

class Think8Vite extends Facade
{
    /**
     * 获取当前Facade对应类名（或者已经绑定的容器对象标识）
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'Sunmking\Think8Vite\Vite';
    }
}
