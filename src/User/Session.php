<?php

namespace PhpSchool\Website\User;

final class Session implements \ArrayAccess
{
    public static function regenerate(): void
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
                session_name(),
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

    /**
     * @param non-empty-string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $_SESSION ?? [])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param string|array|null $value
     */
    public function set(string $key, $value): void
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

    /**
     * @param string $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($_SESSION[$offset]);
    }

    /**
     * @param non-empty-string $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * @param string $offset
     * @param string|array|null $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * @param string $offset
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }
}
