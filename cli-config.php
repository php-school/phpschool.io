<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use DI\ContainerBuilder;

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/vendor/php-di/slim-bridge/src/config.php');
$containerBuilder->addDefinitions(__DIR__ . '/app/config.php');

$container = $containerBuilder->build();
return $container->get(ConsoleRunner::class);