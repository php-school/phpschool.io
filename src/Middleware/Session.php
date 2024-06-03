<?php

declare(strict_types=1);

namespace PhpSchool\Website\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class Session
{
    /**
     * @var array{
     *       name: string,
     *       lifetime: int,
     *       path: string|null,
     *       domain: string|null,
     *       secure: bool,
     *       httponly: bool,
     *       cache_limiter: string
     *   }
     */
    private array $options = [
        'name' => 'phpschool',
        'lifetime' => 0, //until the browser is closed
        'path' => null,
        'domain' => null,
        'secure' => false,
        'httponly' => true,
        'cache_limiter' => 'nocache',
    ];

    /**
     * @param array{
     *       name?: string,
     *       lifetime?: int,
     *       path?: string|null,
     *       domain?: string|null,
     *       secure?: bool,
     *       httponly?: bool,
     *       cache_limiter?: string
     *   } $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['name'])) {
            $this->options['name'] = $options['name'];
        }

        if (isset($options['lifetime'])) {
            $this->options['lifetime'] = $options['lifetime'];
        }

        if (isset($options['path'])) {
            $this->options['path'] = $options['path'];
        }

        if (isset($options['domain'])) {
            $this->options['domain'] = $options['domain'];
        }

        if (isset($options['secure'])) {
            $this->options['secure'] = $options['secure'];
        }

        if (isset($options['httponly'])) {
            $this->options['httponly'] = $options['httponly'];
        }

        if (isset($options['cache_limiter'])) {
            $this->options['cache_limiter'] = $options['cache_limiter'];
        }
    }

    private function start(): void
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            return;
        }

        $options = $this->options;
        $current = session_get_cookie_params();

        $lifetime = (int) ($options['lifetime'] ?: $current['lifetime']);
        $path     = $options['path'] ?: $current['path'];
        $domain   = $options['domain'] ?: $current['domain'];
        $secure   = (bool) $options['secure'];
        $httponly = (bool) $options['httponly'];

        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        session_name($options['name']);
        session_cache_limiter($options['cache_limiter']);
        session_start();
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->start();

        return $handler->handle($request);
    }
}
