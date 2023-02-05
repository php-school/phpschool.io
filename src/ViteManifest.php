<?php

namespace PhpSchool\Website;

class ViteManifest
{
    /**
     * @var array<string, array{
     *     file: string,
     *     src?: string,
     *     imports?: array<string>,
     *     css?: array<string>
     * }>
     */
    private array $manifest;

    public function __construct()
    {
        /** @var string $content */
        $content = file_get_contents(__DIR__ . '/../public/dist/manifest.json');

        /** @var array<string, array{file: string, src?: string, imports?: array<string>}> $data */
        $data = json_decode($content, true, JSON_THROW_ON_ERROR);
        $this->manifest = $data;
    }

    public function assetUrl(string $entry): string
    {
        return isset($this->manifest[$entry])
            ? '/dist/' . $this->manifest[$entry]['file']
            : '';
    }

    /**
     * @return array<string>
     */
    public function importsUrls(string $entry): array
    {
        return array_map(function (string $import) {
            return '/dist/' . $this->manifest[$import]['file'] ?? ''; /** @phpstan-ignore-line */
        }, $this->manifest[$entry]['imports'] ?? []);
    }

    /**
     * @return array<string>
     */
    public function cssUrls(string $entry): array
    {
        return array_map(function (string $file) {
            return '/dist/' . $file;
        }, $this->manifest[$entry]['css'] ?? []);
    }
}
