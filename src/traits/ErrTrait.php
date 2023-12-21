<?php

namespace Sunmking\Think8Queue\traits;

trait ErrTrait
{
    /**
     * @var string
     */
    protected string $err = '';

    /**
     * @param string|null $err
     * @return bool
     */
    public function setError(?string$err=null): bool
    {
        $this->err = $err?:'未知错误';
        return false;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        $err = $this->err;
        $this->err=null;
        return $err;
    }

}