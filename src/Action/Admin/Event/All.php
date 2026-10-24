<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class All
{
    use JsonUtils;

    public function __construct(
        private readonly EventRepository $repository,
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        return $this->withJson(
            ['events' => $this->repository->findAll()],
            $response,
        );
    }
}
