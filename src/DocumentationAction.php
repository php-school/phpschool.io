<?php

namespace PhpSchool\Website;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

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

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response)
    {
        $group      = $request->getAttribute('route')->getArgument('group', 'index');
        $section    = $request->getAttribute('route')->getArgument('section', 'index');

        $document   = $this->documentation->findSectionByGroupAndSection($group, $section);
        $docContent = $this->renderer->fetch($document->getTemplateFile());

        $inner = $this->renderer->fetch('docs.phtml', [
            'docs'      => $this->documentation,
            'content'   => $docContent,
            'doc'       => $document,
        ]);

        return $this->renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle'       => sprintf('Documentation - %s', $document->getTitle()),
            'pageDescription' => sprintf('Documentation - %s', $document->getTitle()),
            'content'         => $inner
        ]);
    }
}
