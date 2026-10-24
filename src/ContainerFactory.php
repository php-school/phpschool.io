<?php

declare(strict_types=1);

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

        $dotEnv = \Dotenv\Dotenv::createMutable(__DIR__ . '/../');
        $dotEnv->load();

        $config = include __DIR__ . '/../app/config.php';

        Collection::macro('ifEmpty', function (callable $callback) {
            /** @var Collection<array-key, mixed> $this */
            if ($this->isEmpty()) {
                $callback($this);
            }

            return $this;
        });

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(include __DIR__ . '/../vendor/php-school/php-workshop/app/config.php');
        // Not compiled: php-workshop's listener definitions are closures, which PHP-DI can't compile.
        $containerBuilder->addDefinitions($config);

        return $containerBuilder->build();
    }
}
