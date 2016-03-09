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

$config = [
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
    'config' => [
        'displayErrorDetails' => true, // set to false in production
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'links' => [
            'github'        => 'https://github.com/php-school/learn-you-php',
            'twitter'       => 'https://twitter.com/PHPSchoolTeam',
            'slack'         => 'https://phpschool.herokuapp.com',
            'discussions'   => 'https://github.com/php-school/discussions',
            'workshop'      => 'https://github.com/php-school/php-workshop',
        ],

        'cacheDir'          => __DIR__ . '/../cache',
        'enablePageCache'   => true,
    ],

    //slim settings
    'settings.displayErrorDetails' => true,
 ];

if (file_exists(__DIR__ . '/dev-config.php')) {
    $config = array_replace_recursive($config, include __DIR__ . '/dev-config.php');
}

return $config;
