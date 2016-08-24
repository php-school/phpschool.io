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

        //clear route cache
        if (file_exists(__DIR__ . '/../../var/cache/router.php')) {
            unlink(__DIR__ . '/../../var/cache/router.php');
        }

        $output->writeln('<info>FPC Cleared!</info>');
    }
}
