<?php

namespace PhpSchool\Website\Command;

use PhpSchool\Website\Cache;

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
