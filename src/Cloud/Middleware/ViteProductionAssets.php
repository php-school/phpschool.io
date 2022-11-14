<?php

namespace PhpSchool\Website\Cloud\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class ViteProductionAssets
{
    /** @var array<string, array{file: string, src?: string, imports?: array<string>}>  */
    private array $manifest;

    public function __construct(private readonly PhpRenderer $renderer)
    {
        $content = file_get_contents(__DIR__ . '/../../../public/dist/manifest.json');
        $this->manifest = json_decode($content, true);
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->renderer->addJs('cloud', $this->assetUrl('main.js'), ['type' => 'module', 'crossorigin']);

        foreach ($this->importsUrls('main.js') as $i => $url) {
            $this->renderer->addPreload($i, $url);
        }

        foreach ($this->cssUrls('main.js') as $i => $url) {
            $this->renderer->appendRemoteCss($i, $url);
        }

        return $handler->handle($request);
    }

    private function assetUrl(string $entry): string
    {
        return isset($this->manifest[$entry])
            ? '/dist/' . $this->manifest[$entry]['file']
            : '';
    }

    private function importsUrls(string $entry): array
    {
        return array_map(function (string $import) {
            return '/dist/' . $this->manifest[$import]['file'] ?? '';
        }, $this->manifest[$entry]['imports'] ?? []);
    }

    private function cssUrls(string $entry): array
    {
        return array_map(function (string $file) {
            return '/dist/' . $file;
        }, $this->manifest[$entry]['css'] ?? []);
    }
}
