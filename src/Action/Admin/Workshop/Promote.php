<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

class Promote
{
    use RedirectUtils;

    private WorkshopRepository $repository;
    private WorkshopFeed $workshopFeed;
    private CacheItemPoolInterface $cache;
    private Messages $messages;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopFeed $workshopFeed,
        CacheItemPoolInterface $cache,
        Messages $messages
    ) {
        $this->workshopFeed = $workshopFeed;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, string $id): MessageInterface
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $this->redirect('/admin/workshop/all');
        }

        $workshop->promoteToCore();

        $this->repository->save($workshop);

        $this->cache->clear();

        try {
            $this->workshopFeed->generate();
            $this->messages->addMessage(
                'admin.success',
                sprintf('Successfully promoted %s to core and regenerated workshop feed!', $workshop->getDisplayName())
            );
        } catch (RuntimeException $e) {
            $this->messages->addMessage(
                'admin.error',
                sprintf('Workshop feed could not be generated. Error: "%s"', $e->getMessage())
            );
        }

        return $this->redirect('/admin/workshop/all');
    }
}
