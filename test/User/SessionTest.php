<?php

namespace PhpSchool\WebsiteTest\User;

use PhpSchool\Website\User\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    public function setUp(): void
    {
        $_SESSION = [
            'a' => '1',
        ];
    }

    public function testGet(): void
    {
        $session = new Session;
        $a = $session->get('a');
        $b = $session->get('b', '2');

        $this->assertEquals('1', $a);
        $this->assertEquals('2', $b);
    }

    public function testSet(): void
    {
        $session = new Session;

        $session->set('c', '3');
        $this->assertEquals('3', $session->get('c'));

        $session->set('d', '4');
        $this->assertEquals('4', $session->get('d'));
    }

    public function testDelete(): void
    {
        $session = new Session;

        $session->set('c', '3');
        $this->assertEquals('3', $session->get('c'));

        $session->delete('c');
        $this->assertNull($session->get('c'));
    }

    public function testClearAll(): void
    {
        $session = new Session;

        $this->assertEquals('1', $session->get('a'));

        $session->clearAll();
        $this->assertNull($session->get('a'));
    }

//    public function testDestroy(): void
//    {
//        @session_start();
//        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
//        @Session::destroy(); // silence headers already sent warning
//        $this->assertEquals(PHP_SESSION_NONE, session_status());
//    }
}
