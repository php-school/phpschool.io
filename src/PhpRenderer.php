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
        $editLink = '';
        if (null !== $file) {
            $editLink = sprintf(
                '<a title="Edit this page on GitHub!" target="_blank" class="edit-on-gh" href="%s">%s</a>',
                $this->getAttribute('links')['github-website'] . '/tree/master/templates/' . $file,
                $this->fetch('includes/icon.phtml', ['name' => "edit"])
            );
        }

        $format      = '<h2 id="%s" class="doc__title">%s<a class="anchor" href="#%s">#</a>%s';
        $format     .= '<a href="#page-top" class="back-to-top">^ TOP</a></h2>';

        return sprintf($format, $id, $title, $id, $editLink);
    }

    public function backToTop() : string
    {
        return '<a href="#page-top" class="back-to-top">^ TOP</a>';
    }
}
