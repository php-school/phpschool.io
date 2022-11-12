<?php

namespace PhpSchool\Website\Cloud\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class Styles
{
    private const VITE_HOST = 'http://localhost:5133';
    /** @var array<string, array{file: string, src?: string, imports?: array<string>}>  */
    private array $manifest;

    private PhpRenderer $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;

        $content = file_get_contents(__DIR__ . '/../../../public/dist/manifest.json');
        $this->manifest = json_decode($content, true);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer
            ->removeCss('code-blocks')
            ->removeJs('jquery')
            ->removeJs('main-js')
            ->removeJs('highlight-js');

        $this->js('main.js');
        $this->jsPreloadImports('main.js');
        $this->css('main.js');

        return $handler->handle($request);
    }

    private function isDevMode(): bool
    {
        return isset($_ENV['DEV_MODE']) && $_ENV['DEV_MODE'] === 'true';
    }

    private function js(string $entry): void
    {
        $url = $this->isDevMode()
            ? self::VITE_HOST . '/' . $entry
            : $this->assetUrl($entry);

        if (!$url) {
            return;
        }

        $this->renderer->addJs('cloud', $url, ['type' => 'module', 'crossorigin']);
    }

    private function jsPreloadImports(string $entry): void
    {
        if ($this->isDevMode()) {
            return;
        }

        foreach ($this->importsUrls($entry) as $i => $url) {
            $this->renderer->addPreload($i, $url);
        }
    }

    private function importsUrls(string $entry): array
    {
        return array_map(function (string $import) {
            return '/dist/' . $this->manifest[$import]['file'] ?? '';
        }, $this->manifest[$entry]['imports'] ?? []);
    }

    private function css(string $entry): void
    {
        if ($this->isDevMode()) {
            return;
        }

        foreach ($this->cssUrls($entry) as $i => $url) {
            $this->renderer->appendRemoteCss($i, $url);
        }
    }

    private function cssUrls(string $entry): array
    {
        return array_map(function (string $file) {
            return '/dist/' . $file;
        }, $this->manifest[$entry]['css'] ?? []);
    }

    private function assetUrl(string $entry): string
    {
        return isset($this->manifest[$entry])
            ? '/dist/' . $this->manifest[$entry]['file']
            : '';
    }
}
