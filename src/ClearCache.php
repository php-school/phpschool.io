<?php

namespace PhpSchool\Website;

/**
 * Class ClearCache
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ClearCache
{
    /**
     * @param Cache $cache
     */
    public function __invoke(Cache $cache)
    {
        $cache->flush();
    }
}
