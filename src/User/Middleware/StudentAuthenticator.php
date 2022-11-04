<?php

namespace PhpSchool\Website\User\Middleware;

use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Session;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class StudentAuthenticator
{
    use RedirectUtils;

    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, RequestHandler $handler): MessageInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        if ($route === null) {
            // Student likely accessing a nonexistent route. Calling next middleware.
            return $handler->handle($request);
        }

        //if not a cloud route
        if (strpos($route->getPattern(), '/cloud') !== 0) {
            return $handler->handle($request);
        }

        //if on cloud home page allow guests
        if ($route->getPattern() === '/cloud') {
            return $handler->handle($request);
        }

        //if on any other cloud route, student must be logged in
        if ($this->session->get('student') instanceof Student) {
            return $handler->handle($request);
        }

        return $this->redirect('/cloud');
    }
}
