<?php

namespace PhpSchool\Website\Cloud\Middleware;

use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\ViteManifest;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ViteProductionAssets
{
    public function __construct(
        private readonly PhpRenderer $renderer,
        private readonly ViteManifest $manifest
    ) {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer->addJs('cloud', $this->manifest->assetUrl('cloud.js'), ['type' => 'module', 'crossorigin']);

        foreach ($this->manifest->importsUrls('cloud.js') as $i => $url) {
            $this->renderer->addPreload($i, $url);
        }

        foreach ($this->manifest->cssUrls('cloud.js') as $i => $url) {
            $this->renderer->appendRemoteCss($i, $url);
        }

        return $handler->handle($request);
    }
}
