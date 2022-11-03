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
            ->removeJs('main-js')
            ->removeJs('highlight-js')
            ->appendLocalCss('tailwindcss', __DIR__ . '/../../../public/css/cloud.min.css')
            ->addJs('vue', 'https://unpkg.com/vue@3/dist/vue.global.js');

        return $handler->handle($request);
    }
}
