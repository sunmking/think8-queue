<?php

namespace Sunmking\Think8Queue\Tests\Unit\Events;

use Sunmking\Think8Queue\Events\EventDispatcher;
use Sunmking\Think8Queue\Tests\TestCase;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = new EventDispatcher();
    }

    public function testListenWithClosure(): void
    {
        $called = false;
        $this->dispatcher->listen('test.event', function() use (&$called) {
            $called = true;
        });
        
        $this->dispatcher->dispatch('test.event');
        
        $this->assertTrue($called);
    }

    public function testListenWithClass(): void
    {
        $this->dispatcher->listen('test.event', TestEventListener::class);
        
        $this->dispatcher->dispatch('test.event', 'test_data');
        
        $this->assertTrue(TestEventListener::$called);
        $this->assertEquals('test_data', TestEventListener::$data);
    }

    public function testDispatchWithArguments(): void
    {
        $receivedArgs = [];
        $this->dispatcher->listen('test.event', function(...$args) use (&$receivedArgs) {
            $receivedArgs = $args;
        });
        
        $this->dispatcher->dispatch('test.event', 'arg1', 'arg2', 'arg3');
        
        $this->assertEquals(['arg1', 'arg2', 'arg3'], $receivedArgs);
    }

    public function testRemoveListener(): void
    {
        $called = false;
        $listener = function() use (&$called) {
            $called = true;
        };
        
        $this->dispatcher->listen('test.event', $listener);
        $this->dispatcher->remove('test.event', $listener);
        $this->dispatcher->dispatch('test.event');
        
        $this->assertFalse($called);
    }

    public function testRemoveAllListeners(): void
    {
        $called = false;
        $this->dispatcher->listen('test.event', function() use (&$called) {
            $called = true;
        });
        
        $this->dispatcher->remove('test.event');
        $this->dispatcher->dispatch('test.event');
        
        $this->assertFalse($called);
    }

    public function testNonExistentEvent(): void
    {
        $this->dispatcher->dispatch('non.existent.event');
        
        // 应该不抛出异常
        $this->assertTrue(true);
    }
}

class TestEventListener
{
    public static bool $called = false;
    public static mixed $data = null;

    public function __invoke($data = null): void
    {
        self::$called = true;
        self::$data = $data;
    }
}

