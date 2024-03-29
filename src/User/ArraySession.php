<?php

declare(strict_types=1);

namespace PhpSchool\Website\User;

class ArraySession implements SessionStorageInterface
{
    /**
     * @param array<string, mixed> $data
     */
    public function __construct(private array $data = []) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    public function delete(string $key): void
    {
        if (array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }
    }

    public function clearAll(): void
    {
        $this->data = [];
    }

    public function offsetExists(string $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(string $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(string $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(string $offset): void
    {
        $this->delete($offset);
    }
}
