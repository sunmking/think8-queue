<?php

namespace Sunmking\Think8Queue\Tests\Helpers;

use think\queue\Job;

class MockJob implements Job
{
    private int $attempts = 1;
    private bool $deleted = false;
    private ?int $releaseDelay = null;

    public function __construct(int $attempts = 1)
    {
        $this->attempts = $attempts;
    }

    public function attempts(): int
    {
        return $this->attempts;
    }

    public function delete(): void
    {
        $this->deleted = true;
    }

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function release(?int $delay = null): void
    {
        $this->releaseDelay = $delay;
    }

    public function getReleaseDelay(): ?int
    {
        return $this->releaseDelay;
    }

    public function getJobId(): string
    {
        return 'test-job-id';
    }

    public function getQueue(): string
    {
        return 'test-queue';
    }

    public function getRawBody(): string
    {
        return json_encode(['test' => 'data']);
    }

    public function payload(): array
    {
        return ['test' => 'data'];
    }

    public function timeout(): int
    {
        return 60;
    }

    public function retryUntil(): ?int
    {
        return null;
    }

    public function getConnectionName(): string
    {
        return 'default';
    }

    public function getJob(): mixed
    {
        return $this;
    }

    public function getReservedJob(): mixed
    {
        return $this;
    }

    public function isReserved(): bool
    {
        return true;
    }

    public function isDeletedOrReleased(): bool
    {
        return $this->deleted || $this->releaseDelay !== null;
    }

    public function hasFailed(): bool
    {
        return false;
    }

    public function markAsFailed(): void
    {
        // Mock implementation
    }

    public function failed($e): void
    {
        // Mock implementation
    }

    public function getMaxTries(): int
    {
        return 3;
    }

    public function getMaxExceptions(): int
    {
        return 3;
    }

    public function getBackoff(): array
    {
        return [];
    }

    public function getRetryUntil(): ?int
    {
        return null;
    }

    public function getTimeout(): int
    {
        return 60;
    }

    public function getRetryAfter(): int
    {
        return 60;
    }

    public function getConnectionName(): string
    {
        return 'default';
    }

    public function getJob(): mixed
    {
        return $this;
    }

    public function getReservedJob(): mixed
    {
        return $this;
    }

    public function isReserved(): bool
    {
        return true;
    }

    public function isDeletedOrReleased(): bool
    {
        return $this->deleted || $this->releaseDelay !== null;
    }

    public function hasFailed(): bool
    {
        return false;
    }

    public function markAsFailed(): void
    {
        // Mock implementation
    }

    public function failed($e): void
    {
        // Mock implementation
    }

    public function getMaxTries(): int
    {
        return 3;
    }

    public function getMaxExceptions(): int
    {
        return 3;
    }

    public function getBackoff(): array
    {
        return [];
    }

    public function getRetryUntil(): ?int
    {
        return null;
    }

    public function getTimeout(): int
    {
        return 60;
    }

    public function getRetryAfter(): int
    {
        return 60;
    }
}

