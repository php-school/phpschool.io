<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Promote
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
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var Messages
     */
    private $messages;

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

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, $id)
    {
        try {
            $workshop = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/workshops/all');
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

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/workshops/all');
    }
}
