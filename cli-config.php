<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use PhpSchool\Website\ContainerFactory;

require __DIR__ . '/vendor/autoload.php';

ini_set('display_errors', 1);

$container = (new ContainerFactory)();
return $container->get(ConsoleRunner::class);