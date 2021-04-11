<?php

namespace PhpSchool\Website\Action\Admin;

use Psr\Cache\CacheItemPoolInterface;
use Slim\Flash\Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ClearCache
{
    private CacheItemPoolInterface $cache;
    private Messages $messages;

    public function __construct(CacheItemPoolInterface $cache, Messages $messages)
    {
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $this->cache->clear();

        $this->messages->addMessage('admin.success', 'Successfully cleared full page cache');

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin');
    }
}
