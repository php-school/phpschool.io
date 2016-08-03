<?php

namespace PhpSchool\Website;

use Cache\Bridge\DoctrineCacheBridge;
use DI\ContainerBuilder;
use Interop\Container\ContainerInterface;
use Predis\Client;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Doctrine\Common\Cache\Cache as DoctrineCache;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ContainerFactory
{
    public function __invoke() : ContainerInterface
    {
        $config = include __DIR__ . '/../app/config.php';

        if (file_exists(__DIR__ . '/../app/local-config.php')) {
            $config = array_replace_recursive($config, include __DIR__ . '/../app/local-config.php');
        }

        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/../vendor/php-di/slim-bridge/src/config.php');
        $containerBuilder->addDefinitions($config);

        $cache = $config['config']['enableCache']
            ? new RedisAdapter(new Client(['host' => $config['config']['redisHost']]), 'default')
            : new NullAdapter();

        $doctrineCache = new DoctrineCacheBridge($cache);
        $containerBuilder->setDefinitionCache($doctrineCache);

        $container = $containerBuilder->build();
        $container->set('cache', $cache);
        $container->set(DoctrineCache::class, $doctrineCache);

        return $container;
    }
}
