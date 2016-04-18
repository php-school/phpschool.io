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

    /**
     * @param string $template
     * @return bool
     */
    public function exists(string $template) : bool
    {
        return is_file($this->templatePath . $template);
    }
}
