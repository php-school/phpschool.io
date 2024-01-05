<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\User\FlashMessages;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use RuntimeException;

class Promote
{
    use JsonUtils;

    private WorkshopRepository $repository;
    private WorkshopFeed $workshopFeed;
    private CacheItemPoolInterface $cache;
    private FlashMessages $messages;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopFeed $workshopFeed,
        CacheItemPoolInterface $cache,
        FlashMessages $messages
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
            return $this->withJson(
                [
                    'error' => 'Could not find workshop with id: ' . $id
                ],
                $response,
                404
            );
        }

        $workshop->promoteToCore();

        $this->repository->save($workshop);

        $this->cache->clear();

        try {
            $this->workshopFeed->generate();
            return $this->jsonSuccess($response);
        } catch (RuntimeException $e) {
            return $this->withJson(
                [
                    'error' => sprintf('Workshop feed could not be generated. Error: "%s"', $e->getMessage())
                ],
                $response,
                500
            );
        }
    }
}
