<?php

namespace PhpSchool\Website\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AdminStyle
{
    private PhpRenderer $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $fontAwesomeCss = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css';
        $this->renderer
            ->removeCss('main-css')
            ->removeCss('code-blocks')
            ->removeJs('main-js')
            ->removeJs('highlight-js')
            ->appendLocalCss('bootstrap', __DIR__ . '/../../public/css/bootstrap.min.css')
            ->appendRemoteCss('fontawesome', $fontAwesomeCss)
            ->appendLocalCss('gentelella', __DIR__ . '/../../public/css/gentelella.min.css')
            ->appendLocalCss('admin', __DIR__ . '/../../public/css/admin.css')
            ->addJs('bootstrap', '/js/bootstrap.min.js')
            ->addJs('gentelella', '/js/gentelella.min.js');

        return $handler->handle($request);
    }
}
