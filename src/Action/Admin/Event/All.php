<?php

namespace PhpSchool\Website\Action\Admin\Event;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
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
     * @var WorkshopRepository
     */
    private $repository;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(
        EventRepository $repository,
        PhpRenderer $renderer
    ) {
        $this->repository = $repository;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response)
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
