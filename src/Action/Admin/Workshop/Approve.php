<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Slim\Flash\Messages;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Approve
{
    /**
     * @var WorkshopRepository
     */
    private $repository;

    /**
     * @var WorkshopFeed
     */
    private $workshopFeed;

    /**
     * @var Messages
     */
    private $messages;

    public function __construct(WorkshopRepository $repository, WorkshopFeed $workshopFeed, Messages $messages)
    {
        $this->workshopFeed = $workshopFeed;
        $this->repository = $repository;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, $id)
    {
        $workshop = $this->repository->findById($id);
        $workshop->approve();

        $this->repository->save($workshop);

        $this->workshopFeed->generate();

        $this->messages->addMessage(
            'admin',
            sprintf('Successfully approved %s and regenerated workshop feed.', $workshop->getDisplayName())
        );

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/workshops/all');
    }
}
