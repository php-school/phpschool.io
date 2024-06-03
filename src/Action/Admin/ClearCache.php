<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\Action\JsonUtils;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ClearCache
{
    use JsonUtils;

    public function __construct(private readonly CacheItemPoolInterface $cache) {}

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $this->cache->clear();

        return $this->jsonSuccess($response);
    }
}
