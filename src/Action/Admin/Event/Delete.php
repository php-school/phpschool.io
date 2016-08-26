<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Repository\EventRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Delete
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var Messages
     */
    private $messages;


    public function __construct(
        EventRepository $repository,
        CacheItemPoolInterface $cache,
        Messages $messages
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, $id)
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/events/all');
        }

        $this->repository->remove($event);

        $this->cache->clear();

        $this->messages->addMessage(
            'admin.success',
            sprintf('Successfully removed event %s', $event->getName())
        );


        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/events/all');
    }
}
