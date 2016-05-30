<?php

namespace PhpSchool\Website;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class DocumentationAction
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocumentationAction
{
    /**
     * @var Documentation
     */
    private $documentation;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(Documentation $documentation, PhpRenderer $renderer)
    {
        $this->documentation = $documentation;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $group      = $request->getAttribute('route')->getArgument('group', 'index');
        $section    = $request->getAttribute('route')->getArgument('section', 'index');

        $document   = $this->documentation->findSectionByGroupAndSection($group, $section);
        $docContent = $this->renderer->fetch($document->getTemplateFile(), ['doc' => $document]);

        $title = $this->renderer->renderDocHeader(
            $this->slugify($document->getTitle()),
            $document->getTitle(),
            $document->getTemplateFile()
        );

        $this->renderer->addJs('https://cdn.jsdelivr.net/docsearch.js/1/docsearch.min.js');
        $this->renderer->prependCss('https://cdn.jsdelivr.net/docsearch.js/1/docsearch.min.css');

        $inner = $this->renderer->fetch('docs.phtml', [
            'docs'      => $this->documentation,
            'content'   => $title . $docContent,
            'doc'       => $document,
        ]);

        return $this->renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle'       => sprintf('Documentation - %s', $document->getTitle()),
            'pageDescription' => sprintf('Documentation - %s', $document->getTitle()),
            'content'         => $inner
        ]);
    }

    private function slugify(string $title) : string
    {
        return str_replace(' ', '-', strtolower($title));
    }
}
