<?php

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Events
{
    use JsonUtils;

    private EventRepository $repository;

    public function __construct(
        EventRepository $repository,
    ) {
        $this->repository = $repository;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'events' => $this->repository->findUpcoming(),
                'previousEvents' => $this->repository->findPrevious(),
            ],
            $response
        );
    }
}