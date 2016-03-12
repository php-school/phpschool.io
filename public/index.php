<?php

use DI\ContainerBuilder;
use PhpSchool\Website\Cache;
use PhpSchool\Website\DocGenerator;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/../vendor/php-di/slim-bridge/src/config.php');
$containerBuilder->addDefinitions(__DIR__ . '/../app/config.php');

$app = new \Slim\App($containerBuilder->build());

//cache all the pages
$container  = $app->getContainer();
$cache      = $container->get(Cache::class);

if ($container->get('config')['enablePageCache']) {
    $app->add(function (Request $request, Response $response, callable $next) use ($cache) {
        $response = $next($request, $response);

        if ($response->getStatusCode() === 200) {
            $cache->add($request->getUri()->getPath(), (string) $response->getBody(), 200, [], Cache::WEEK);
        }

        return $response;
    });
    $app->add($cache);
}


$app->get('/', function (Request $request, Response $response, PhpRenderer $renderer) {
    $inner = $renderer->fetch('home.phtml');
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Home',
        'pageDescription' => 'Learn PHP the right way... the open source way. PHP School Open Source Learning for PHP',
        'content'         => $inner
    ]);
});

$app->get('/install', function (Request $request, Response $response, PhpRenderer $renderer) {
    $renderer->addJs('//code.jquery.com/jquery-1.12.0.min.js');
    $renderer->addJs('/js/main.js');

    $inner = $renderer->fetch('install.phtml');
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Installation instructions',
        'pageDescription' => 'Installation instructions for PHPSchool',
        'content'         => $inner
    ]);
});

$app->get('/docs', function (Request $request, Response $response, PhpRenderer $renderer) {


    $renderer->prependCss('/css/solarized-light.css');
    $renderer->addJs('/js/highlight.min.js');
    $renderer->addJs('//code.jquery.com/jquery-1.12.0.min.js');
    $renderer->addJs('/js/main.js');

    $inner = $renderer->fetch('docs.phtml');

    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Documentation',
        'pageDescription' => 'Documentation',
        'content'         => $inner
    ]);
});

$app->get('/api-docs', function (Request $request, Response $response, PhpRenderer $renderer, DocGenerator $docGenerator) {

    $apiCacheFile = __DIR__ . '/../cache/api-docs.json';
    if (file_exists($apiCacheFile)) {
        $docs = json_decode(file_get_contents($apiCacheFile), true);
    } else {
        $docs = $docGenerator->generate();
        file_put_contents($apiCacheFile, json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    $renderer->prependCss('/css/solarized-light.css');
    $renderer->addJs('/js/highlight.min.js');
    $renderer->addJs('//code.jquery.com/jquery-1.12.0.min.js');
    $renderer->addJs('/js/main.js');

    $inner = $renderer->fetch('api-docs.phtml', ['docs' => $docs]);

    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'API - Documentation',
        'pageDescription' => 'PHPSchool API Documentation',
        'content'         => $inner
    ]);
});

// Run app
$app->run();

