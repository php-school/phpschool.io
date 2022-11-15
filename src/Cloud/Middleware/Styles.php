<?php

namespace PhpSchool\Website\Cloud\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class Styles
{
    private PhpRenderer $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer
            ->removeCss('code-blocks')
            ->removeJs('jquery')
            ->removeJs('main-js')
            ->removeJs('highlight-js');

        return $handler->handle($request);
    }
}
