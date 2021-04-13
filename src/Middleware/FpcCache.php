<?php

namespace PhpSchool\Website\Middleware;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class FpcCache
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $key = sprintf('fpc-route-%s-%s', $this->encodeUrl($request->getUri()->getPath()), $request->getMethod());

        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            return $this->unserialize($item->get());
        }

        $response = $handler->handle($request);

        if ($this->canSave($request, $response)) {
            $item->set($this->serialize($response));
            $item->expiresAt(new \DateTime('now + 1 month'));
            $this->cache->save($item);
        }

        return $response;
    }

    /**
     * This is not robust enough
     */
    private function encodeUrl(string $urlPath): string
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $urlPath)));
    }

    private function serialize(Response $response): string
    {
        return json_encode([
            'body'      => (string) $response->getBody(),
            'headers'   => $response->getHeaders(),
        ]);
    }

    private function unserialize(string $responseData): Response
    {
        $responseData = json_decode($responseData, true);
        $response = new GuzzleResponse(200, $responseData['headers']);
        $response->getBody()->write($responseData['body']);
        return $response;
    }

    /**
     * Check whether the response can be saved or not.
     */
    private function canSave(Request $request, Response $response): bool
    {
        if ($request->getMethod() !== 'GET') {
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        if ($this->hasNoCacheHeader($response)) {
            return false;
        }

        return true;
    }

    private function hasNoCacheHeader(Response $response): bool
    {
        $cacheControl = $response->getHeaderLine('Cache-Control');

        if (!$cacheControl) {
            return false;
        }

        if ((stripos($cacheControl, 'no-cache') !== false || stripos($cacheControl, 'no-store') !== false)) {
            return true;
        }

        return false;
    }
}
