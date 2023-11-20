<?php

namespace PhpSchool\Website\Action\Admin\Workshop;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use RuntimeException;

class RegenerateFeed
{
    use JsonUtils;

    private WorkshopFeed $workshopFeed;

    public function __construct(
        WorkshopFeed $workshopFeed,
    ) {
        $this->workshopFeed = $workshopFeed;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
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
