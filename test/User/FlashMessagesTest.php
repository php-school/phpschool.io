<?php

namespace PhpSchool\WebsiteTest\User;

use PhpSchool\Website\User\ArraySession;
use PhpSchool\Website\User\FlashMessages;
use PHPUnit\Framework\TestCase;

class FlashMessagesTest extends TestCase
{
    public function testGetMessagesFromPreviousRequest(): void
    {
        $storage = new ArraySession(['slimFlash' => ['Test']]);
        $flash = new FlashMessages($storage);

        $this->assertEquals(['Test'], $flash->getMessages());
    }

    public function testAddMessageForCurrentRequest(): void
    {
        $storage = new ArraySession(['slimFlash' => []]);
        $flash   = new FlashMessages($storage);

        $flash->addMessageNow('key', 'value');

        $messages = $flash->getMessages();
        $this->assertEquals(['value'], $messages['key']);

        $this->assertTrue($storage->offsetExists('slimFlash'));
        $this->assertEmpty($storage->offsetGet('slimFlash'));
    }

    public function testAddMessageForNextRequest(): void
    {
        $storage = new ArraySession(['slimFlash' => []]);
        $flash   = new FlashMessages($storage);

        $flash->addMessage('key', 'value');

        $this->assertTrue($storage->offsetExists('slimFlash'));
        $this->assertEquals(['key' => ['value']], $storage->offsetGet('slimFlash'));
    }

    public function testGetEmptyMessagesFromPreviousRequest(): void
    {
        $storage = new ArraySession([]);
        $flash = new FlashMessages($storage);

        $this->assertEquals([], $flash->getMessages());
    }

    public function testSetMessagesForCurrentRequest(): void
    {
        $storage = new ArraySession(['slimFlash' => [ 'error' => ['An error']]]);
        $flash = new FlashMessages($storage);
        $flash->addMessageNow('error', 'Another error');
        $flash->addMessageNow('success', 'A success');
        $flash->addMessageNow('info', 'An info');

        $messages = $flash->getMessages();
        $this->assertEquals(['An error', 'Another error'], $messages['error']);
        $this->assertEquals(['A success'], $messages['success']);
        $this->assertEquals(['An info'], $messages['info']);

        $this->assertTrue($storage->offsetExists('slimFlash'));
        $this->assertEmpty($storage->offsetGet('slimFlash'));
    }

    public function testSetMessagesForNextRequest(): void
    {
        $storage = new ArraySession([]);

        $flash = new FlashMessages($storage);
        $flash->addMessage('Test', 'Test');
        $flash->addMessage('Test', 'Test2');

        $this->assertTrue($storage->offsetExists('slimFlash'));
        $this->assertEquals(['Test' => ['Test', 'Test2']], $storage->offsetGet('slimFlash'));
    }

    public function testGetMessageFromKey(): void
    {
        $storage = new ArraySession(['slimFlash' => [ 'Test' => ['Test', 'Test2']]]);
        $flash = new FlashMessages($storage);

        $this->assertEquals(['Test', 'Test2'], $flash->getMessage('Test'));
    }

    public function testGetMessageFromKeyIncludingCurrent(): void
    {
        $storage = new ArraySession(['slimFlash' => [ 'Test' => ['Test', 'Test2']]]);
        $flash = new FlashMessages($storage);
        $flash->addMessageNow('Test', 'Test3');

        $messages = $flash->getMessages();

        $this->assertEquals(['Test', 'Test2','Test3'], $flash->getMessage('Test'));
    }

    public function testHasMessage(): void
    {
        $storage = new ArraySession(['slimFlash' => []]);
        $flash = new FlashMessages($storage);
        $this->assertFalse($flash->hasMessage('Test'));

        $storage = new ArraySession(['slimFlash' => [ 'Test' => ['Test']]]);
        $flash = new FlashMessages($storage);
        $this->assertTrue($flash->hasMessage('Test'));
    }

    public function testClearMessages(): void
    {
        $storage = new ArraySession(['slimFlash' => [ 'Test' => ['Test']]]);
        $flash = new FlashMessages($storage);
        $flash->addMessageNow('Now', 'hear this');
        $this->assertTrue($flash->hasMessage('Test'));
        $this->assertTrue($flash->hasMessage('Now'));

        $flash->clearMessages();
        $this->assertFalse($flash->hasMessage('Test'));
        $this->assertFalse($flash->hasMessage('Now'));
    }

    public function testSettingCustomStorageKey(): void
    {
        $storage = new ArraySession(['some-key' => [ 'Test' => ['Test']]]);
        $flash = new FlashMessages($storage);
        $this->assertFalse($flash->hasMessage('Test'));

        $flash = new FlashMessages($storage, 'some-key');
        $this->assertTrue($flash->hasMessage('Test'));
    }
}
