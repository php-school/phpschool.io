<?php

namespace PhpSchool\Website\Command;

use PhpSchool\Website\Cache;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class ClearCache
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ClearCache
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
     * Clear the FPC cache
     */
    public function __invoke()
    {
        $this->cache->clear();
    }
}
