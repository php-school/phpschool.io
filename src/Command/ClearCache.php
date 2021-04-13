<?php

namespace PhpSchool\Website\Command;

use PhpSchool\Website\Cache;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCache
{
    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    public function __invoke(OutputInterface $output): void
    {
        $this->cache->clear();

        //clear route cache
        if (file_exists(__DIR__ . '/../../var/cache/router.php')) {
            unlink(__DIR__ . '/../../var/cache/router.php');
        }

        $output->writeln('<info>FPC Cleared!</info>');
    }
}
