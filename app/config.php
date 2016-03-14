<?php

use function DI\factory;
use Interop\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpSchool\Website\Cache;
use PhpSchool\Website\Command\ClearCache;
use PhpSchool\Website\Command\GenerateDoc;
use PhpSchool\Website\DocGenerator;
use PhpSchool\Website\Middleware\FpcCache;
use Psr\Log\LoggerInterface;
use PhpSchool\Website\PhpRenderer;
use Stash\Driver\BlackHole;
use Stash\Driver\FileSystem;
use Stash\Pool;

$config = [
    'console' => factory(function (ContainerInterface $c) {
        $app = new Silly\Edition\PhpDi\Application('PHP School Website', null, $c);
        $app->command('generate-docs', $c->get(GenerateDoc::class));
        $app->command('clear-cache', $c->get(ClearCache::class));
        return $app;
    }),
    'app' => factory(function (ContainerInterface $c) {
        $app = new \Slim\App($c);
        $app->add($c->get(FpcCache::class));

        return $app;
    }),
    FpcCache::class => factory(function (ContainerInterface $c) {
        return new FpcCache($c->get('cache.fpc'));
    }),
    'cache.fpc' => factory(function (ContainerInterface $c) {

        if (!$c->get('config')['enablePageCache']) {
            return new Pool(new BlackHole);
        }

        return new Pool(new FileSystem(['path' => $c->get('config')['cacheDir']]));
    }),
    PhpRenderer::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['renderer'];

        $renderer = new PhpRenderer($settings['template_path'], [
            'links' => $c->get('config')['links'],
            'route' => $c->get('request')->getUri()->getPath(),
        ]);

        //default CSS
        $renderer->appendCss('/css/core.css');
        $renderer->appendCss('https://fonts.googleapis.com/css?family=Open+Sans');
        return $renderer;
    }),
    LoggerInterface::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['logger'];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor);
        $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
        return $logger;
    }),
    DocGenerator::class => \DI\object(),

    //commands
    GenerateDoc::class => \DI\object(),
    ClearCache::class => factory(function (ContainerInterface $c) {
        return new ClearCache($c->get('cache.fpc'));
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
            'github'         => 'https://github.com/php-school/learn-you-php',
            'twitter'        => 'https://twitter.com/PHPSchoolTeam',
            'slack'          => 'https://phpschool.herokuapp.com',
            'discussions'    => 'https://github.com/php-school/discussions',
            'workshop'       => 'https://github.com/php-school/php-workshop',
            'github-website' => 'https://github.com/php-school/phpschool.io',
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
