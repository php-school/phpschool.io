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
     * @var CommonMarkConverter
     */
    private $markdownConverter;

    /**
     * SlimRenderer constructor.
     *
     * @param CommonMarkConverter $markdownConverter
     * @param string $templatePath
     * @param array $attributes
     */
    public function __construct(CommonMarkConverter $markdownConverter, $templatePath = "", $attributes = [])
    {
        $this->markdownConverter = $markdownConverter;
        $this->templatePath = $templatePath;
        $this->attributes = $attributes;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function prependCss(string $id, string $cssFile) : PhpRenderer
    {
        array_unshift($this->css, ['id' => $id, 'url' => $cssFile]);
        return $this;
    }

    /**
     * @param string $id
     * @param string $cssFile
     * @return PhpRenderer
     */
    public function appendCss(string $id, string $cssFile) : PhpRenderer
    {
        $this->css[] = ['id' => $id, 'url' => $cssFile];
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
     * @return array
     */
    public function getCss() : array
    {
        return array_map(function (array $css) {
            return $css['url'];
        }, $this->css);
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

    public function markdownToHtml($markdown) : string
    {
        return $this->markdownConverter->convertToHtml($markdown);
    }
}
