<?php

namespace PhpSchool\Website;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PhpRenderer
{
    private string $templatePath;

    private array $attributes;

    /**
     * @var list<array{id: string, url: string, type: "local"|"remote"}>
     */
    private array $css = [];

    /**
     * @var list<array{id: string, url: string}>
     */
    private array $js = [];

    /**
     * @var list<array{id: string, url: string}>
     */
    private array $preload = [];

    private static int $jsonFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR;

    public function __construct(string $templatePath = '', array $attributes = [])
    {
        $this->templatePath = rtrim($templatePath, '/\\') . '/';
        $this->attributes = $attributes;
    }

    public function addAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key): mixed
    {
        if (!isset($this->attributes[$key])) {
            return false;
        }

        return $this->attributes[$key];
    }

    public function prependLocalCss(string $id, string $cssFile): PhpRenderer
    {
        array_unshift($this->css, ['id' => $id, 'url' => $cssFile, 'type' => 'local']);
        return $this;
    }

    public function appendLocalCss(string $id, string $cssFile): PhpRenderer
    {
        $this->css[] = ['id' => $id, 'url' => $cssFile, 'type' => 'local'];
        return $this;
    }

    public function prependRemoteCss(string $id, string $cssFile): PhpRenderer
    {
        array_unshift($this->css, ['id' => $id, 'url' => $cssFile, 'type' => 'remote']);
        return $this;
    }

    public function appendRemoteCss(string $id, string $cssFile): PhpRenderer
    {
        $this->css[] = ['id' => $id, 'url' => $cssFile, 'type' => 'remote'];
        return $this;
    }

    public function removeCss(string $id): PhpRenderer
    {
        $this->css = array_values(array_filter($this->css, function (array $css) use ($id) {
            return $css['id'] !== $id;
        }));
        return $this;
    }

    public function renderCss(): string
    {
        return implode("\n", array_map(function (array $css) {
            if ($css['type'] === 'local') {
                return sprintf('<style>%s</style>', file_get_contents($css['url']));
            } else {
                return sprintf('<link rel="stylesheet" type="text/css"  href="%s">', $css['url']);
            }
        }, $this->css));
    }

    public function addJs(string $id, string $jsFile, array $tags = ['defer']): PhpRenderer
    {
        $this->js[] = ['id' => $id, 'url' => $jsFile, 'tags' => $tags];
        return $this;
    }

    public function removeJs(string $id): PhpRenderer
    {
        $this->js = array_values(array_filter($this->js, function (array $js) use ($id) {
            return $js['id'] !== $id;
        }));
        return $this;
    }

    public function getJs(): array
    {
        return collect($this->js)
            ->map(function (array $js) {
                $tags = collect($js['tags'] ?? [])
                    ->map(function (string $value, string|int $key) {
                        return is_int($key) ? $value : sprintf('%s="%s"', $key, $value);
                    })
                    ->implode(' ');

                return ['src' => $js['url'], 'tags' => $tags];
            })
            ->toArray();
    }

    public function addPreload(string $id, string $file): PhpRenderer
    {
        $this->preload[] = ['id' => $id, 'url' => $file];
        return $this;
    }

    public function getPreload(): array
    {
        return array_map(fn ($preload) => $preload['url'], $this->preload);
    }

    public function removePreload(string $id): PhpRenderer
    {
        $this->css = array_values(array_filter($this->preload, function (array $preload) use ($id) {
            return $preload['id'] !== $id;
        }));
        return $this;
    }

    public function slugClass(string $class): string
    {
        return str_replace('\\', '-', strtolower($class));
    }

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $output = $this->fetch($template, $data, true);
        $response->getBody()->write($output);
        return $response;
    }

    public function fetch(string $template, array $data = [], bool $useLayout = false): string
    {
        return $this->fetchTemplate($template, $data);
    }

    public function fetchTemplate(string $template, array $data = []): string
    {
        if (isset($data['template'])) {
            throw new \InvalidArgumentException('Duplicate template key found');
        }

        if (!$this->templateExists($template)) {
            throw new \InvalidArgumentException('View cannot render "' . $template
                . '" because the template does not exist');
        }

        $data = array_merge($this->attributes, $data);
        try {
            ob_start();
            $this->protectedIncludeScope($this->templatePath . $template, $data);
            $output = ob_get_clean();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return $output;
    }

    public function templateExists(string $template): bool
    {
        $path = $this->templatePath . $template;
        return is_file($path) && is_readable($path);
    }

    /**
     * @psalm-suppress UnresolvableInclude
     */
    private function protectedIncludeScope(string $template, array $data): void
    {
        extract($data);
        include func_get_arg(0);
    }

    public function slug(string $string): string
    {
        return (new AsciiSlugger())->slug($string)->toString();
    }

    public function json(mixed $var): string
    {
        return json_encode($var, self::$jsonFlags);
    }
}
