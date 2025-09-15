<?php

namespace Sunmking\Think8Queue\Events;

use think\queue\Job;

abstract class JobEvent
{
    public function __construct(
        public readonly Job $job,
        public readonly array $data,
        public readonly ?string $queue = null
    ) {}
}

class JobProcessing extends JobEvent {}

class JobProcessed extends JobEvent {}

class JobFailed extends JobEvent
{
    public function __construct(
        Job $job,
        array $data,
        public readonly \Throwable $exception,
        ?string $queue = null
    ) {
        parent::__construct($job, $data, $queue);
    }
}
