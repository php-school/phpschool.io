<?php

namespace PhpSchool\Website\Cloud\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ViteDevAssets
{
    private const VITE_HOST = 'http://localhost:5133';

    public function __construct(private readonly PhpRenderer $renderer)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer->addJs('cloud', self::VITE_HOST . '/main.js', ['type' => 'module', 'crossorigin']);

        return $handler->handle($request);
    }
}
