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
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function delete(string $key): void
    {
        if (array_key_exists($key, $_SESSION)) {
            unset($_SESSION[$key]);
        }
    }

    public function clearAll(): void
    {
        $_SESSION = [];
    }

    public function offsetExists($offset)
    {
        return isset($_SESSION[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
