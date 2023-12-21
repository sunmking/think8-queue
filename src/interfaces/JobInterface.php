<?php

namespace Sunmking\Think8Queue\interfaces;

use think\queue\Job;

interface JobInterface
{
    /**
     * @param Job $job
     * @param $data
     * @return void
     */
    public function fire(Job $job,$data): void;
}