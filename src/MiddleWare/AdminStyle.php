<?php

namespace PhpSchool\Website\Middleware;

use PhpSchool\Website\PhpRenderer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class AdminStyle
{
    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $this->renderer
            ->removeCss('main-css')
            ->removeCss('code-blocks')
            ->removeJs('main-js')
            ->removeJs('highlight-js')
            ->appendCss('bootstrap', '/css/bootstrap.min.css')
            ->appendCss('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css')
            ->appendCss('gentelella', '/css/gentelella.min.css')
            ->appendCss('admin', '/css/admin.css')
            ->addJs('bootstrap', '/js/bootstrap.min.js')
            ->addJs('gentelella', '/js/gentelella.min.js');

        return $next($request, $response);
    }
}
