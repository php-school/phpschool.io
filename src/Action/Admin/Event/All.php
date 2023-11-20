<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class All
{
    use JsonUtils;

    private EventRepository $repository;
    private PhpRenderer $renderer;

    public function __construct(
        EventRepository $repository,
        PhpRenderer $renderer
    ) {
        $this->repository = $repository;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response): Response
    {
       return $this->withJson(
           ['events' => $this->repository->findAll()],
           $response
       );
    }
}
