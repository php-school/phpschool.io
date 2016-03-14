<?php

namespace PhpSchool\Website\Middleware;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Headers;
use Slim\Http\Response;

/**
 * Class FpcCache
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class FpcCache
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        $key  = sprintf('fpc-route-%s', $this->encodeUrl($request->getUri()->getPath()));

        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            $response = $this->unserialize($item->get());
            return $response;
        }

        $response = $next($request, $response);

        if ($this->canSave($request, $response)) {

            $item->set($this->serialize($response));
            $item->setTTL(new \DateTime('now + 1 month'));
            $this->cache->save($item);
        }

        return $response;
    }

    /**
     * This is not robust enough
     *
     * @param string $urlPath
     * @return string
     */
    private function encodeUrl($urlPath)
    {

        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $urlPath)));
    }

    /**
     * @param ResponseInterface $response
     * @return array
     */
    private function serialize(ResponseInterface $response)
    {
        return json_encode([
            'body'      => (string) $response->getBody(),
            'headers'   => $response->getHeaders(),
        ]);
    }

    /**
     * @param string $responseData
     */
    private function unserialize(string $responseData)
    {
        $responseData = json_decode($responseData, true);
        $response = new Response(200, new Headers($responseData['headers']));
        $response->getBody()->write($responseData['body']);
        return $response;
    }

    /**
     * Check whether the response can be saved or not.
     *
     * @param ServerRequestInterface  $request
     * @param ResponseInterface $response
     *
     * @return bool
     */
    private function canSave(ServerRequestInterface $request, ResponseInterface $response)
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

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    private function hasNoCacheHeader(ResponseInterface $response)
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
