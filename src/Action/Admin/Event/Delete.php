<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Repository\EventRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;
use Slim\Flash\Messages;

class Delete
{
    private EventRepository $repository;
    private CacheItemPoolInterface $cache;
    private Messages $messages;

    public function __construct(
        EventRepository $repository,
        CacheItemPoolInterface $cache,
        Messages $messages
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response, PhpRenderer $renderer, string $id): Response
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin/event/all');
        }

        $this->repository->remove($event);

        $this->cache->clear();

        $this->messages->addMessage(
            'admin.success',
            sprintf('Successfully removed event %s', $event->getName())
        );

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin/event/all');
    }
}
