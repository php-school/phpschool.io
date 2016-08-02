<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopRepository;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class All
{
    /**
     * @var PhpRenderer
     */
    private $renderer;

    /**
     * @var WorkshopRepository
     */
    private $repository;

    public function __construct(WorkshopRepository $repository, PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response)
    {
        $inner = $this->renderer->fetch('admin/workshop/all.phtml', [
            'workshops' => $this->repository->findAll()
        ]);
        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'All Workshops',
            'pageDescription' => 'All Workshops',
            'content'         => $inner
        ]);
    }
}
