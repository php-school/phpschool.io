<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopRepository;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Requests
{
    private PhpRenderer $renderer;
    private WorkshopRepository $repository;

    public function __construct(WorkshopRepository $repository, PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $inner = $this->renderer->fetch('admin/workshop/requests.phtml', [
            'workshops' => $this->repository->findAllPendingApproval()
        ]);
        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'Workshop submissions',
            'pageDescription' => 'Workshop submissions',
            'content'         => $inner
        ]);
    }
}
