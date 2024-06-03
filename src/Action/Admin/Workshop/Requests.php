<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Repository\WorkshopRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Requests
{
    use JsonUtils;

    private WorkshopRepository $repository;

    public function __construct(WorkshopRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(['workshops' => $this->repository->findAllPendingApproval()], $response);
    }
}
