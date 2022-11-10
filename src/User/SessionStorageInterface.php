<?php

namespace PhpSchool\Website\User;

interface SessionStorageInterface
{
    /**
     * @param non-empty-string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @param string|array|object|null $value
     */
    public function set(string $key, $value): void;

    public function delete(string $key): void;

    public function clearAll(): void;

    /**
     * @param string $offset
     */
    public function offsetExists($offset): bool;

    /**
     * @param non-empty-string $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed;

    /**
     * @param string $offset
     * @param string|array|null $value
     */
    public function offsetSet($offset, $value): void;

    /**
     * @param string $offset
     */
    public function offsetUnset($offset): void;
}
