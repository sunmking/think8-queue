<?php

namespace Sunmking\Think8Queue\Middleware;

use Sunmking\Think8Queue\Middleware\MiddlewareInterface;
use think\queue\Job;

class RetryMiddleware implements MiddlewareInterface
{
    public function handle(Job $job, array $data, callable $next): mixed
    {
        $maxAttempts = $data['attempts'] ?? 3;
        $retryAfter = $data['retry_after'] ?? 90;
        
        try {
            return $next($job, $data);
        } catch (\Throwable $e) {
            if ($job->attempts() >= $maxAttempts) {
                $job->delete();
                throw $e;
            }
            
            $job->release($retryAfter);
            throw $e;
        }
    }
}
