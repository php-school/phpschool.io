<?php

namespace PhpSchool\Website;

use League\CommonMark\CommonMarkConverter;
use Slim\Views\PhpRenderer as SlimPhpRenderer;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class PhpRenderer extends SlimPhpRenderer
{
    /**
     * @var array
     */
    private $css = [];

    /**
     * @var array
     */
    private $js = [];

    /**
     *
     * @param string $templatePath
     * @param array $attributes
     */
    public function __construct($templatePath = "", $attributes = [])
    {
        $this->templatePath = $templatePath;
        $this->attributes = $attributes;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function prependLocalCss(string $id, string $cssFile) : PhpRenderer
    {
        array_unshift($this->css, ['id' => $id, 'url' => $cssFile, 'type' => 'local']);
        return $this;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function appendLocalCss(string $id, string $cssFile) : PhpRenderer
    {
        $this->css[] = ['id' => $id, 'url' => $cssFile, 'type' => 'local'];
        return $this;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function prependRemoteCss(string $id, string $cssFile) : PhpRenderer
    {
        array_unshift($this->css, ['id' => $id, 'url' => $cssFile, 'type' => 'remote']);
        return $this;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function appendRemoteCss(string $id, string $cssFile) : PhpRenderer
    {
        $this->css[] = ['id' => $id, 'url' => $cssFile, 'type' => 'remote'];
        return $this;
    }

    /**
     * @param string $id
     * @return PhpRenderer
     */
    public function removeCss(string $id) : PhpRenderer
    {
        $this->css = array_values(array_filter($this->css, function (array $css) use ($id) {
            return $css['id'] !== $id;
        }));
        return $this;
    }

    /**
     * @return string
     */
    public function renderCss() : string
    {
        return implode("\n", array_map(function (array $css) {
            if ($css['type'] === 'local') {
                return sprintf('<style>%s</style>', file_get_contents($css['url']));
            } elseif ($css['type'] === 'remote') {
                return sprintf('<link rel="stylesheet" type="text/css"  href="%s">', $css['url']);
            }
        }, $this->css));
    }

    /**
     * @param string $id
     * @param string $jsFile
     * @return PhpRenderer
     */
    public function addJs(string $id, string $jsFile) : PhpRenderer
    {
        $this->js[] = ['id' => $id, 'url' => $jsFile];
        return $this;
    }

    /**
     * @param string $id
     * @return PhpRenderer
     */
    public function removeJs(string $id) : PhpRenderer
    {
        $this->js = array_values(array_filter($this->js, function (array $js) use ($id) {
            return $js['id'] !== $id;
        }));
        return $this;
    }

    /**
     * @return array
     */
    public function getJs() : array
    {
        return array_map(function (array $js) {
            return $js['url'];
        }, $this->js);
    }

    public function renderDocHeader(string $id, string $title, string $file = null) : string
    {
        return $this->fetch('includes/doc-title.phtml', ['id' => $id, 'title' => $title, 'file' => $file]);
    }

    public function renderContentHeader(string $id, string $title) : string
    {
        return $this->fetch('includes/content-header.phtml', ['id' => $id, 'title' => $title]);
    }

    public function slugClass(string $class) : string
    {
        return str_replace('\\', '-', strtolower($class));
    }
}
