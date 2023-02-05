<?php

namespace PhpSchool\Website\User;

use ArrayAccess;
use RuntimeException;
use InvalidArgumentException;

class FlashMessages
{
    /**
     * Messages from previous request
     *
     * @var array<string, array<string>>
     */
    private array $fromPrevious = [];

    /**
     * Messages for current request
     *
     * @var array<string, array<string>>
     */
    private array $forNow = [];

    private SessionStorageInterface $storage;

    /**
     * @var non-empty-string
     */
    private string $storageKey = 'slimFlash';

    public function __construct(SessionStorageInterface $storage, string $storageKey = null)
    {
        if ($storageKey) {
            $this->storageKey = $storageKey;
        }

        $this->storage = $storage;

        // Load messages from previous request
        if ($this->storage->offsetExists($this->storageKey) && is_array($this->storage->offsetGet($this->storageKey))) {
            $this->fromPrevious = $this->storage->offsetGet($this->storageKey);
        }

        $this->storage->offsetSet($this->storageKey, []);
    }

    /**
     * Add flash message for the next request
     */
    public function addMessage(string $key, string $message): void
    {
        /**
         * @var array<array<string>> $data
         */
        $data = $this->storage->offsetGet($this->storageKey);

        if (!isset($data[$key])) {
            $data[$key] = [];
        }

        $data[$key][] = $message;

        $this->storage->offsetSet($this->storageKey, $data);
    }

    /**
     * Add flash message for current request
     */
    public function addMessageNow(string $key, string $message): void
    {
        if (!isset($this->forNow[$key])) {
            $this->forNow[$key] = [];
        }

        $this->forNow[$key][] = $message;
    }

    /**
     * Get flash messages
     *
     * @return array<string, array<string>> Messages to show for current request
     */
    public function getMessages(): array
    {
        $messages = $this->fromPrevious;

        foreach ($this->forNow as $key => $values) {
            if (!isset($messages[$key])) {
                $messages[$key] = [];
            }

            foreach ($values as $value) {
                $messages[$key][] = $value;
            }
        }

        return $messages;
    }

    /**
     * Get Flash Message
     * @return array<string>
     */
    public function getMessage(string $key): ?array
    {
        $messages = $this->getMessages();

        return (isset($messages[$key])) ? $messages[$key] : null;
    }

    public function hasMessage(string $key): bool
    {
        $messages = $this->getMessages();
        return isset($messages[$key]);
    }

    public function clearMessages(): void
    {
        if ($this->storage->offsetExists($this->storageKey)) {
            $this->storage->offsetSet($this->storageKey, []);
        }

        $this->fromPrevious = [];
        $this->forNow = [];
    }
}
