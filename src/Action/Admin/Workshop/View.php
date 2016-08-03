<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class View
{
    /**
     * @var WorkshopRepository
     */
    private $repository;

    /**
     * @var WorkshopInstallRepository
     */
    private $workshopInstallRepository;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopInstallRepository $workshopInstallRepository,
        PhpRenderer $renderer
    ) {
        $this->repository = $repository;
        $this->workshopInstallRepository = $workshopInstallRepository;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, $id)
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/workshops/all');
        }

        $this->renderer->addJs('charts', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.1/Chart.bundle.min.js');

        $inner = $this->renderer->fetch('admin/workshop/view.phtml', [
            'workshop' => $workshop,
            'installs' => $this->workshopInstallRepository->findInstallsInLast30Days($workshop),
        ]);

        return $this->renderer->render($response, 'layouts/admin.phtml', [
            'pageTitle'       => $workshop->getDisplayName(),
            'pageDescription' => $workshop->getDisplayName(),
            'content'         => $inner
        ]);
    }
}
