<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\User\FlashMessages;
use PhpSchool\Website\Workshop\EmailNotifier;
use PhpSchool\Website\WorkshopFeed;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Psr\Log\LoggerInterface;
use RuntimeException;

class Approve
{
    use JsonUtils;

    private WorkshopRepository $repository;
    private WorkshopFeed $workshopFeed;
    private CacheItemPoolInterface $cache;
    private FlashMessages $messages;
    private EmailNotifier $emailNotifier;
    private LoggerInterface $logger;

    public function __construct(
        WorkshopRepository $repository,
        WorkshopFeed $workshopFeed,
        CacheItemPoolInterface $cache,
        FlashMessages $messages,
        EmailNotifier $emailNotifier,
        LoggerInterface $logger
    ) {
        $this->workshopFeed = $workshopFeed;
        $this->repository = $repository;
        $this->cache = $cache;
        $this->messages = $messages;
        $this->emailNotifier = $emailNotifier;
        $this->logger = $logger;
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
                500
            );
        }

        $workshop->approve();

        $this->repository->save($workshop);

        $this->cache->clear();

        try {
            $this->emailNotifier->approved($workshop);
        } catch (RuntimeException $e) {
            $this->logger->error(sprintf('Email could not be sent. Error: "%s"', $e->getMessage()));
        }

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
