<?php

namespace PhpSchool\Website\User;

/**
 * @implements \ArrayAccess<string, mixed>
 */
final class Session implements \ArrayAccess, SessionStorageInterface
{
    public function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function destroy(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name() ?: 'phpschoolsess',
                '',
                time() - 42000,
                (string) $params["path"],
                (string) $params["domain"],
                (bool) $params["secure"],
                (bool) $params["httponly"]
            );
        }

        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $_SESSION ?? [])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        if (array_key_exists($key, $_SESSION ?? [])) {
            unset($_SESSION[$key]);
        }
    }

    public function clearAll(): void
    {
        $_SESSION = [];
    }

    public function offsetExists($offset): bool
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->delete($offset);
    }
}
