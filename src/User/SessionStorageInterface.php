<?php

declare(strict_types=1);

namespace PhpSchool\Website\User;

interface SessionStorageInterface
{
    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    public function delete(string $key): void;

    public function clearAll(): void;

    public function offsetExists(string $offset): bool;

    public function offsetGet(string $offset): mixed;

    public function offsetSet(string $offset, mixed $value): void;

    public function offsetUnset(string $offset): void;
}
