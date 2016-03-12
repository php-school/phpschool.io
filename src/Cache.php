<?php

namespace PhpSchool\Website;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SNicholson\SlimFileCache\File;
use SNicholson\SlimFileCache\FileHandler;

/**
 * Class Cache
 * @package PhpSchool\Website
 */
class Cache
{
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const NEVER = -1;

    private $directory;

    /**
     * Takes the slim app and then the directory where the cache is to be saved
     * @param string $cacheDirectory
     */
    public function __construct($cacheDirectory = __DIR__ . '/../cache/')
    {
        if (!file_exists($cacheDirectory)) {
            mkdir($cacheDirectory, 0775, true);
        }

        $this->directory = $cacheDirectory;
        $this->fileHandler = new FileHandler($cacheDirectory);
    }

    /**
     * Slim required __invoke magic method for middleware
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $requestPath = $request->getUri()->getPath();
        /** @var File $cache */
        $cache = $this->get($requestPath);
        if ($cache instanceof File) {
            $response = $response->withStatus($cache->getStatus());
            foreach ($cache->getHeaders() as $header => $value) {
                $response = $response->withHeader($header, $value);
            }
            $response->getBody()->write($cache->getContent());
            return $response;
        }
        $response = $next($request, $response);
        return $response;
    }

    /**
     * Removes all cache entries in the given directory
     */
    public function flush()
    {
        $this->fileHandler->deleteAll();
    }

    /**
     * Returns the cached string for the given cacheKey
     *
     * @param $cacheKey
     *
     * @return bool|File
     */
    public function get($cacheKey)
    {
        return $this->fileHandler->read($cacheKey);
    }

    /**
     * Adds a cache entry with a given key, content and for a set amount of time
     * The time by default for the cache is an hour
     *
     * @param       $cacheKey
     * @param       $content
     * @param int   $status
     * @param array $headers
     * @param int   $expires
     *
     * @throws CacheFileSystemException
     */
    public function add($cacheKey, $content, $status = 200, $headers = [], $expires = Cache::HOUR)
    {
        $file = File::create();
        $file->setStatus($status);
        $file->setContent($content);
        $file->setRoute($cacheKey);
        $file->setHeaders($headers);
        if ($expires > 0) {
            $file->setExpires(time() + $expires);
        } else {
            $file->setExpires($expires);
        }
        $this->fileHandler->write($file);
    }

    /**
     * Removes the cache entry for the given key
     * @param $cacheKey
     */
    public function remove($cacheKey)
    {
        $this->fileHandler->delete($cacheKey);
    }

    /**
     * Returns the directory the cache is set to save into
     * @return mixed
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
