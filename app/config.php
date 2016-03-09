<?php

use function DI\factory;
use Interop\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpSchool\Website\Cache;
use PhpSchool\Website\DocGenerator;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;

return [
    PhpRenderer::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['renderer'];

        return new PhpRenderer($settings['template_path'], [
            'links' => $c->get('config')['links'],
            'route' => $c->get('request')->getUri()->getPath(),
        ]);
    }),
    LoggerInterface::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['logger'];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor);
        $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
        return $logger;
    }),
    DocGenerator::class => \DI\object(),
    Cache::class => factory(function (ContainerInterface $c) {
        return new Cache($c->get('config')['cacheDir']);
    }),
 ];
