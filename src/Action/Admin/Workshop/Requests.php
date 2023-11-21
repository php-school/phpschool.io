<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Requests
{
    use JsonUtils;

    private PhpRenderer $renderer;
    private WorkshopRepository $repository;

    public function __construct(WorkshopRepository $repository, PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(['workshops' => $this->repository->findAllPendingApproval()], $response);
    }
}
