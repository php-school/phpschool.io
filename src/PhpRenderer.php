<?php

namespace PhpSchool\Website;

use League\CommonMark\CommonMarkConverter;
use Slim\Views\PhpRenderer as SlimPhpRenderer;
/**
 * Class PhpRenderer
 * @package PhpSchool\Website
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
     * @param string $cssFile
     */
    public function prependCss(string $cssFile)
    {
        array_unshift($this->css, $cssFile);
    }

    /**
     * @param string $cssFile
     */
    public function appendCss(string $cssFile)
    {
        $this->css[] = $cssFile;
    }

    /**
     * @return array
     */
    public function getCss() : array
    {
        return $this->css;
    }

    /**
     * @param string $jsFile
     */
    public function addJs(string $jsFile)
    {
        $this->js[] = $jsFile;
    }

    /**
     * @return array
     */
    public function getJs() : array
    {
        return $this->js;
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
