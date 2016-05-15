<?php

namespace PhpSchool\Website;

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
}
