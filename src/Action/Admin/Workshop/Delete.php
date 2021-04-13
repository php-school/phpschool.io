<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

class Delete
{
    private WorkshopRepository $repository;
    private WorkshopInstallRepository $installRepository;
    private WorkshopFeed $workshopFeed;
    private CacheItemPoolInterface $cache;
    private Messages $messages;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopInstallRepository $installRepository,
        WorkshopFeed $workshopFeed,
        CacheItemPoolInterface $cache,
        Messages $messages
    ) {
        $this->workshopFeed = $workshopFeed;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->messages = $messages;
        $this->installRepository = $installRepository;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, $id): Response
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/workshop/all');
        }

        $this->installRepository->removeAllByWorkshop($workshop);
        $this->repository->remove($workshop);

        $this->cache->clear();

        try {
            $this->workshopFeed->generate();
            $this->messages->addMessage(
                'admin.success',
                sprintf('Successfully removed %s and regenerated workshop feed!', $workshop->getDisplayName())
            );
        } catch (RuntimeException $e) {
            $this->messages->addMessage(
                'admin.error',
                sprintf('Workshop feed could not be generated. Error: "%s"', $e->getMessage())
            );
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/workshop/all');
    }
}
