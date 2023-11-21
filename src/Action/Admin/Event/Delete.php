<?php

namespace PhpSchool\Website\Action\Admin\Event;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Repository\EventRepository;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;

class Delete
{
    use JsonUtils;

    private EventRepository $repository;
    private CacheItemPoolInterface $cache;

    public function __construct(
        EventRepository $repository,
        CacheItemPoolInterface $cache,
    ) {
        $this->repository = $repository;
        $this->cache = $cache;
    }

    public function __invoke(Request $request, Response $response, string $id): Response
    {
        try {
            $event = $this->repository->findById($id);
        } catch (RuntimeException $e) {
            return $this->withJson(
                [
                    'error' => 'Could not find workshop with id: ' . $id
                ],
                $response,
                500
            );
        }

        $this->repository->remove($event);

        $this->cache->clear();

        return $this->jsonSuccess($response);
    }
}
