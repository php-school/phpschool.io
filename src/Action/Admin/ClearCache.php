<?php

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\FlashMessages;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ClearCache
{
    use RedirectUtils;

    private CacheItemPoolInterface $cache;
    private FlashMessages $messages;

    public function __construct(CacheItemPoolInterface $cache, FlashMessages $messages)
    {
        $this->cache = $cache;
        $this->messages = $messages;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $this->cache->clear();

        $this->messages->addMessage('admin.success', 'Successfully cleared full page cache');

        return $this->redirect('/admin');
    }
}
