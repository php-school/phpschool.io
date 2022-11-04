<?php

namespace PhpSchool\Website\User\Middleware;

use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\AdminAuthenticationService;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class AdminAuthenticator
{
    use RedirectUtils;

    private AdminAuthenticationService $authenticationService;

    public function __construct(AdminAuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function __invoke(Request $request, RequestHandler $handler): MessageInterface
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

        return $this->redirect('/login');
    }
}
