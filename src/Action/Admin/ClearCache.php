<?php

namespace PhpSchool\Website\Action\Admin;

use Psr\Cache\CacheItemPoolInterface;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ClearCache
{

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @var Messages
     */
    private $messages;

    public function __construct(CacheItemPoolInterface $cache, Messages $messages)
    {
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response) : Response
    {
        $this->cache->clear();

        $this->messages->addMessage('admin.success', 'Successfully cleared full page cache');

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin');
    }
}
