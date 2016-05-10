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

    public function renderDocTitle(string $id, string $title, string $file = null) : string
    {
        $options = [
            'classes' => ['doc__title'],
        ];

        if (null !== $file) {
            $options['file'] = $file;
        }

        return $this->renderHeader($id, $title, $options);
    }

    public function renderDocHeader(string $id, string $title) : string
    {
        return $this->renderHeader($id, $title);
    }

    private function renderHeader(string $id, string $title, array $options = []) : string
    {
        $editLink = '';
        if (isset($options['file'])) {
            $editLink = sprintf(
                '<a title="Edit this page on GitHub!" target="_blank" class="edit-on-gh" href="%s">%s</a>',
                $this->getAttribute('links')['github-website'] . '/tree/master/templates/' . $options['file'],
                $this->fetch('includes/icon.phtml', ['name' => "edit"])
            );
        }
        $classes = '';
        if (isset($options['classes']) && is_array($options['classes'])) {
            $classes = implode(' ', $options['classes']);
        }

        $format      = '<h2 id="%s" class="%s">%s<a class="anchor" href="#%s">#</a>%s';
        $format     .= '<a href="#page-top" class="back-to-top">^ TOP</a></h2>';

        return sprintf($format, $id, $classes, $title, $id, $editLink);
    }

    public function backToTop() : string
    {
        return '<a href="#page-top" class="back-to-top">^ TOP</a>';
    }

    public function hashLink(string $id) : string
    {
        return sprintf('<a class="anchor" href="#%s">#</a>', $id);
    }
}
