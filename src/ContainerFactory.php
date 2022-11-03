<?php

namespace PhpSchool\Website;

use DI\ContainerBuilder;
use Illuminate\Support\Collection;
use Psr\Container\ContainerInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ContainerFactory
{
    public function __invoke(): ContainerInterface
    {

        $dotEnv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotEnv->load();

        $config = include __DIR__ . '/../app/config.php';

        Collection::macro('ifEmpty', function (callable $callback) {
            /** @var Collection $this */
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        });

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(include __DIR__ . '/../vendor/php-school/php-workshop/app/config.php');
        $containerBuilder->addDefinitions($config);

        if ($config['config']['enableCache']) {
            $containerBuilder->enableCompilation($config['config']['containerCacheDir']);
        }


        return $containerBuilder->build();
    }
}
