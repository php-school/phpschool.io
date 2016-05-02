<?php

use DI\ContainerBuilder;
use PhpSchool\Website\Cache;
use PhpSchool\Website\DocGenerator;
use PhpSchool\Website\Documentation;
use PhpSchool\Website\DocumentationAction;
use PhpSchool\Website\DocumentationSection;
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

$container  = $containerBuilder->build();
$app        = $container->get('app');

$app->get('/', function (Request $request, Response $response, PhpRenderer $renderer) {
    $inner = $renderer->fetch('home.phtml');
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Home',
        'pageDescription' => 'Learn PHP the right way... the open source way. PHP School Open Source Learning for PHP',
        'content'         => $inner
    ]);
});

$app->get('/install', function (Request $request, Response $response, PhpRenderer $renderer) {
    $inner = $renderer->fetch('install.phtml');
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Installation instructions',
        'pageDescription' => 'Installation instructions for PHPSchool',
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

    $inner = $renderer->fetch('api-docs.phtml', ['docs' => $docs]);

    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'API - Documentation',
        'pageDescription' => 'PHPSchool API Documentation',
        'content'         => $inner
    ]);
});

$app->get('/docs[/{group}[/{section}]]', DocumentationAction::class);

// Run app
$app->run();

