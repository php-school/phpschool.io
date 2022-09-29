<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class All
{
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
        $events = $this->repository->findAll();
        $inner = $this->renderer->fetch('admin/event/all.phtml', [
            'events' => $events,
        ]);

        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => 'All Events',
            'pageDescription' => 'All Events',
            'content'         => $inner
        ]);
    }
}
