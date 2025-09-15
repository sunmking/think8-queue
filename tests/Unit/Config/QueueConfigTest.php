<?php

namespace Sunmking\Think8Queue\Tests\Unit\Config;

use Sunmking\Think8Queue\Config\QueueConfig;
use Sunmking\Think8Queue\Tests\TestCase;

class QueueConfigTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $config = new QueueConfig();
        
        $this->assertEquals('default', $config->get('default_queue'));
        $this->assertEquals(3, $config->get('default_attempts'));
        $this->assertEquals(60, $config->get('default_timeout'));
        $this->assertEquals(0, $config->get('default_priority'));
        $this->assertEquals('qn_', $config->get('prefix'));
        $this->assertEquals(90, $config->get('retry_after'));
    }

    public function testCustomConfiguration(): void
    {
        $customConfig = [
            'default_queue' => 'custom',
            'default_attempts' => 5,
            'default_timeout' => 120,
        ];
        
        $config = new QueueConfig($customConfig);
        
        $this->assertEquals('custom', $config->get('default_queue'));
        $this->assertEquals(5, $config->get('default_attempts'));
        $this->assertEquals(120, $config->get('default_timeout'));
        $this->assertEquals(0, $config->get('default_priority')); // 默认值
    }

    public function testGetWithDefaultValue(): void
    {
        $config = new QueueConfig();
        
        $this->assertEquals('default', $config->get('default_queue'));
        $this->assertEquals('fallback', $config->get('non_existent', 'fallback'));
    }

    public function testSetConfiguration(): void
    {
        $config = new QueueConfig();
        
        $config->set('custom_key', 'custom_value');
        $this->assertEquals('custom_value', $config->get('custom_key'));
        
        $config->set('default_queue', 'updated');
        $this->assertEquals('updated', $config->get('default_queue'));
    }

    public function testGetAllConfiguration(): void
    {
        $config = new QueueConfig(['test' => 'value']);
        $all = $config->all();
        
        $this->assertIsArray($all);
        $this->assertArrayHasKey('test', $all);
        $this->assertEquals('value', $all['test']);
        $this->assertArrayHasKey('default_queue', $all);
    }
}

