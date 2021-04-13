<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Documentation;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;

class DocsAction
{
    private PhpRenderer $renderer;
    private Documentation $documentation;

    public function __construct(PhpRenderer $renderer, Documentation $documentation)
    {
        $this->renderer = $renderer;
        $this->documentation = $documentation;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $route = RouteContext::fromRequest($request)->getRoute();

        $group = $route->getArgument('group', 'index');
        $section = $route->getArgument('section', 'index');

        $document = $this->documentation->findSectionByGroupAndSection($group, $section);
        $docContent = $this->renderer->fetch($document->getTemplateFile(), ['doc' => $document]);

        $title = $this->renderer->renderDocHeader(
            $this->slugify($document->getTitle()),
            $document->getTitle(),
            $document->getTemplateFile()
        );

        $this->renderer->addJs('docsearch', 'https://cdn.jsdelivr.net/docsearch.js/1/docsearch.min.js');

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
