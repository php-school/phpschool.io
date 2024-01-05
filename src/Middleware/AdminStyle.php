<?php

namespace PhpSchool\Website\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AdminStyle
{
    private PhpRenderer $renderer;
    private bool $devMode;

    public function __construct(PhpRenderer $renderer, bool $devMode)
    {
        $this->renderer = $renderer;
        $this->devMode = $devMode;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer
            ->removeCss('code-blocks')
            ->removeJs('online')
            ->removeJs('highlight-js');

        return $handler->handle($request);
    }
}
