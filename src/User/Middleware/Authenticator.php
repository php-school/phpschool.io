<?php

namespace PhpSchool\Website\User\Middleware;

use PhpSchool\Website\User\AuthenticationService;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class Authenticator
{
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if ($route === null) {
            // User likely accessing a nonexistent route. Calling next middleware.
            return $handler->handle($request);
        }

        if (strpos($route->getPattern(), '/admin') !== 0) {
            return $handler->handle($request);
        }

        if ($this->authenticationService->hasIdentity()) {
            return $handler->handle($request);
        }

        return (new \GuzzleHttp\Psr7\Response(302))
            ->withHeader('Location', '/login');
    }
}
