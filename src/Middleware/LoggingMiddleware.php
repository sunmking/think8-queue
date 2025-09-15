<?php

namespace Sunmking\Think8Queue\Middleware;

use Sunmking\Think8Queue\Middleware\MiddlewareInterface;
use think\facade\Log;
use think\queue\Job;

class LoggingMiddleware implements MiddlewareInterface
{
    public function handle(Job $job, array $data, callable $next): mixed
    {
        $startTime = microtime(true);
        
        Log::info('Job started', [
            'job' => get_class($job),
            'data' => $data,
            'attempts' => $job->attempts()
        ]);

        try {
            $result = $next($job, $data);
            
            $duration = microtime(true) - $startTime;
            Log::info('Job completed', [
                'job' => get_class($job),
                'duration' => round($duration, 3) . 's'
            ]);
            
            return $result;
        } catch (\Throwable $e) {
            $duration = microtime(true) - $startTime;
            Log::error('Job failed', [
                'job' => get_class($job),
                'error' => $e->getMessage(),
                'duration' => round($duration, 3) . 's'
            ]);
            
            throw $e;
        }
    }
}
