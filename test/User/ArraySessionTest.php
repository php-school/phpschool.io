<?php

namespace PhpSchool\WebsiteTest\User;

use PhpSchool\Website\User\ArraySession;
use PHPUnit\Framework\TestCase;

class ArraySessionTest extends TestCase
{
    public function testGet(): void
    {
        $session = new ArraySession(['some' => 'value']);

        $this->assertEquals('value', $session->get('some'));
        $this->assertNull($session->get('some-other'));
        $this->assertEquals('default', $session->get('not-here', 'default'));
    }

    public function testOffsetGet(): void
    {
        $session = new ArraySession(['some' => 'value']);

        $this->assertEquals('value', $session->offsetGet('some'));
        $this->assertNull($session->offsetGet('some-other'));
    }

    public function testOffsetExists(): void
    {
        $session = new ArraySession(['some' => 'value']);

        $this->assertTrue($session->offsetExists('some'));
        $this->assertFalse($session->offsetExists('some-other'));
    }

    public function testSet(): void
    {
        $session = new ArraySession(['some' => 'value']);
        $this->assertNull($session->get('some-other'));
        $session->set('some-other', 'value');
        $this->assertEquals('value', $session->get('some-other'));
    }

    public function testOffsetSet(): void
    {
        $session = new ArraySession(['some' => 'value']);
        $this->assertNull($session->get('some-other'));
        $session->offsetSet('some-other', 'value');
        $this->assertEquals('value', $session->get('some-other'));
    }

    public function testDelete(): void
    {
        $session = new ArraySession(['some' => 'value']);
        $this->assertEquals('value', $session->get('some'));
        $session->delete('some');
        $this->assertNull($session->offsetGet('some'));
    }

    public function testOffsetUnset(): void
    {
        $session = new ArraySession(['some' => 'value']);
        $this->assertEquals('value', $session->get('some'));
        $session->offsetUnset('some');
        $this->assertNull($session->offsetGet('some'));
    }

    public function testClearAll(): void
    {
        $session = new ArraySession(['some' => 'value']);
        $session->set('some-other', 'value');
        $session->clearAll();
        $this->assertFalse($session->offsetExists('some'));
        $this->assertFalse($session->offsetExists('some-other'));
    }
}
