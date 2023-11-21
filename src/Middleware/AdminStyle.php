<?php

namespace PhpSchool\Website\Middleware;

use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\ViteManifest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AdminStyle
{
    private PhpRenderer $renderer;
    private ViteManifest $manifest;
    private bool $devMode;

    public function __construct(PhpRenderer $renderer, ViteManifest $manifest, bool $devMode)
    {
        $this->renderer = $renderer;
        $this->manifest = $manifest;
        $this->devMode = $devMode;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $fontAwesomeCss = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css';
        $this->renderer
            ->removeCss('code-blocks')
            ->removeJs('online')
            ->removeJs('highlight-js');

        foreach ($this->manifest->importsUrls('online.js') as $i => $url) {
            $this->renderer->removePreload('online-preload-' . $i, $url);
        }

        foreach ($this->manifest->cssUrls('online.js') as $i => $url) {
            $this->renderer->removeCss('online-preload-' . $i, $url);
        }

        if ($this->devMode) {
            $this->renderer->addJs('admin', 'http://localhost:5133/admin.js', ['type' => 'module', 'crossorigin']);
        } else {
            $this->renderer->addJs('admin', $this->manifest->assetUrl('admin.js'), ['type' => 'module', 'crossorigin']);

            foreach ($this->manifest->importsUrls('admin.js') as $i => $url) {
                $this->renderer->addPreload($i, $url);
            }

            foreach ($this->manifest->cssUrls('admin.js') as $i => $url) {
                $this->renderer->appendRemoteCss($i, $url);
            }
        }

        return $handler->handle($request);
    }
}
