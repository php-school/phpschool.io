<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action;

use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Events
{
    use JsonUtils;

    public function __construct(
        private readonly EventRepository $repository,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            [
                'events' => $this->repository->findUpcoming(),
                'previousEvents' => $this->repository->findPrevious(),
            ],
            $response,
        );
    }
}
