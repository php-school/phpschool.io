<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;

class Delete
{
    use RedirectUtils;
    use JsonUtils;

    public function __construct(
        private readonly WorkshopRepository $repository,
        private readonly WorkshopInstallRepository $installRepository,
        private readonly WorkshopFeed $workshopFeed,
        private readonly CacheItemPoolInterface $cache,
    ) {}

    public function __invoke(Request $request, Response $response, string $id): MessageInterface
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

        $this->installRepository->removeAllByWorkshop($workshop);
        $this->repository->remove($workshop);

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
