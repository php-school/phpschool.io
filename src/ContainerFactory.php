<?php

namespace PhpSchool\Website;

use Cache\Bridge\DoctrineCacheBridge;
use DI\ContainerBuilder;
use Dotenv\Dotenv;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use Predis\Client;
use Predis\Connection\ConnectionException;
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
        $dotEnv = new Dotenv(__DIR__ . '/../');
        $dotEnv->load();

        $config = include __DIR__ . '/../app/config.php';

        Collection::macro('ifEmpty', function (callable $callback) : Collection {
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        });

        $containerBuilder = new ContainerBuilder;
        $containerBuilder->addDefinitions(__DIR__ . '/../vendor/php-di/slim-bridge/src/config.php');
        $containerBuilder->addDefinitions($config);

        $cache = new NullAdapter;
        if ($config['config']['enableCache']) {
            $redisConnection = new Client(['host' => $config['config']['redisHost']]);
            try {
                $redisConnection->connect();
            } catch (ConnectionException $e) {
                throw new \RuntimeException(
                    sprintf(
                        'Could not connect to redis using host: "%s". Message: "%s"',
                        $config['config']['redisHost'],
                        $e->getMessage()
                    )
                );
            }

            $cache = new RedisAdapter($redisConnection, 'default');
        }

        $doctrineCache = new DoctrineCacheBridge($cache);
        $containerBuilder->setDefinitionCache($doctrineCache);

        $container = $containerBuilder->build();
        $container->set('cache', $cache);
        $container->set(DoctrineCache::class, $doctrineCache);

        return $container;
    }
}
