<?php

namespace Sunmking\Think8Queue\Events;

use Closure;

class EventDispatcher
{
    private array $listeners = [];

    public function listen(string $event, Closure|string $listener): self
    {
        $this->listeners[$event][] = $listener;
        return $this;
    }

    public function dispatch(string $event, ...$args): void
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        foreach ($this->listeners[$event] as $listener) {
            if ($listener instanceof Closure) {
                $listener(...$args);
            } elseif (is_string($listener) && class_exists($listener)) {
                (new $listener)(...$args);
            }
        }
    }

    public function remove(string $event, ?Closure $listener = null): self
    {
        if ($listener === null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners[$event] = array_filter(
                $this->listeners[$event] ?? [],
                fn($l) => $l !== $listener
            );
        }
        return $this;
    }
}
