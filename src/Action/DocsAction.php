<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Documentation;
use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Interfaces\RouteInterface;
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

    public function __invoke(Request $request, Response $response): Response
    {
        /** @var RouteInterface $route */
        $route = RouteContext::fromRequest($request)->getRoute();

        $group = (string) $route->getArgument('group', 'index');
        $section = (string) $route->getArgument('section', 'index');

        $document = $this->documentation->findSectionByGroupAndSection($group, $section);
        $docContent = $this->renderer->fetch($document->getTemplateFile(), ['doc' => $document]);

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
