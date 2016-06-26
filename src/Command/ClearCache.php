<?php

namespace PhpSchool\Website\Command;

use PhpSchool\Website\Cache;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
     * @param OutputInterface $output
     */
    public function __invoke(OutputInterface $output)
    {
        $this->cache->clear();
        $output->writeln('<info>FPC Cleared!</info>');
    }
}
