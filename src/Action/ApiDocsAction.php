<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\DocGenerator;
use PhpSchool\Website\PhpRenderer;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class ApiDocsAction
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ApiDocsAction
{

    /**
     * @var PhpRenderer
     */
    private $renderer;

    /**
     * @var DocGenerator
     */
    private $docGenerator;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(PhpRenderer $renderer, DocGenerator $docGenerator, CacheItemPoolInterface $cache)
    {
        $this->renderer = $renderer;
        $this->docGenerator = $docGenerator;
        $this->cache = $cache;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $namespace  = $request->getAttribute('route')->getArgument('namespace');
        $class      = $request->getAttribute('route')->getArgument('class');

        $apiDocs = $this->cache->getItem('api-docs');
        if (!$apiDocs->isHit()) {
            $apiDocs->set($this->docGenerator->generate());
            $this->cache->save($apiDocs);
        }

        $docs = $apiDocs->get();

        $namespace = $this->findNamespace($docs['namespaces'], $namespace);
        $allNamespaces = array_map(function ($namespaceData) {
            return [
                'title' => $namespaceData['namespace'],
                'slug'  => $this->slugify($namespaceData['namespace'])
            ];
        }, $docs['namespaces']);

        if (null === $class) {
            $content = $this->renderer->fetch('api-docs/namespace.phtml', ['namespace' => $namespace]);
        } else {
            $class = $this->findClass($namespace, $class);
            $content = $this->renderer->fetch('api-docs/class.phtml', ['class' => $class]);
        }

        $inner = $this->renderer->fetch('api-docs.phtml', [
            'namespaces'        => $allNamespaces,
            'content'           => $content,
            'currentNamespace'  => $namespace,
        ]);

        return $this->renderer->render($response, 'layouts/layout.phtml', [
            'pageTitle'       => sprintf('API Docs'),
            'pageDescription' => sprintf('PHP School API Documentation'),
            'content'         => $inner
        ]);
    }

    private function findNamespace(array $docs, $namespace = null) : array
    {
        if (null === $namespace) {
            return $docs[0];
        }

        $namespace = str_replace('-', '\\', $namespace);

        foreach ($docs as $namespaceData) {
            if (strtolower($namespaceData['namespace']) === $namespace) {
                return $namespaceData;
            }
        }

        throw new \InvalidArgumentException(sprintf('Namespace: "%s" does not exist', $namespace));
    }

    private function findClass(array $namespace, $class) : array
    {
        $classes = array_merge($namespace['classes'], $namespace['interfaces'], $namespace['abstracts']);

        foreach ($classes as $classData) {
            if (strtolower($classData['name']) === $class) {
                return $classData;
            }
        }

        throw new \InvalidArgumentException(sprintf('Class: "%s" does not exist', $class));
    }

    private function slugify(string $title) : string
    {
        return str_replace('\\', '-', strtolower($title));
    }
}
