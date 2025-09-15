<?php

namespace Sunmking\Think8Queue\Middleware;

use think\queue\Job;

class MiddlewareManager
{
    private array $middlewares = [];

    public function add(MiddlewareInterface|string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function process(Job $job, array $data, callable $handler): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->middlewares),
            function (callable $next, $middleware) {
                return function (Job $job, array $data) use ($middleware, $next) {
                    if (is_string($middleware)) {
                        $middleware = new $middleware();
                    }
                    return $middleware->handle($job, $data, $next);
                };
            },
            $handler
        );

        return $pipeline($job, $data);
    }
}
