<?php

namespace PhpSchool\Website;

class ViteManifest
{
    /** @var array<string, array{file: string, src?: string, imports?: array<string>}>  */
    private array $manifest;

    public function __construct()
    {
        $content = file_get_contents(__DIR__ . '/../public/dist/manifest.json');
        $this->manifest = json_decode($content, true);
    }

    public function assetUrl(string $entry): string
    {
        return isset($this->manifest[$entry])
            ? '/dist/' . $this->manifest[$entry]['file']
            : '';
    }

    public function importsUrls(string $entry): array
    {
        return array_map(function (string $import) {
            return '/dist/' . $this->manifest[$import]['file'] ?? '';
        }, $this->manifest[$entry]['imports'] ?? []);
    }

    public function cssUrls(string $entry): array
    {
        return array_map(function (string $file) {
            return '/dist/' . $file;
        }, $this->manifest[$entry]['css'] ?? []);
    }
}
