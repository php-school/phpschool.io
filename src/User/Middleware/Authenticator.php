<?php

namespace PhpSchool\Website\User\Middleware;

use PhpSchool\Website\User\AuthenticationService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Authenticator
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $route = $request->getAttribute('route', null);

        if ($route === null) {
            // User likely accessing a nonexistent route. Calling next middleware.
            return $next($request, $response);
        }

        if (strpos($route->getPattern(), '/admin') !== 0) {
            return $next($request, $response);
        }

        if ($this->authenticationService->hasIdentity()) {
            return $next($request, $response);
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/login');
    }
}
