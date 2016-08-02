<?php

use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\ApiDocsAction;
use PhpSchool\Website\Action\DocsAction;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Cache;
use PhpSchool\Website\ContainerFactory;
use PhpSchool\Website\DocumentationAction;
use PhpSchool\Website\Middleware\AdminStyle;
use PhpSchool\Website\User\AuthenticationService;
use PhpSchool\Website\User\Middleware\Authenticator;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Slim\Flash\Messages;

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

$app = (new ContainerFactory)()->get('app');

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

$app->get('/api-docs[/{namespace}[/{class}]]', ApiDocsAction::class);
$app->get('/docs[/{group}[/{section}]]', DocsAction::class);

$app
    ->group('/admin', function () {
        $this->get('', function (Request $request, Response $response, PhpRenderer $renderer) {
            return $renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area',
                'pageDescription' => 'Admin Area',
                'content'         => 'Welcome to the Admin!'
            ]);
        });

        $this->get('/workshops/new', Requests::class);
        $this->get('/workshops/all', All::class);
        $this->get('/workshop/approve/{id}', Approve::class);
        $this->get(
            '/regenerate',
            function (Request $request, Response $response, Messages $messages, WorkshopFeed $workshopFeed) {
                try {
                    $workshopFeed->generate();
                    $messages->addMessage('admin.success', 'Workshop feed was successfully regenerated!');
                } catch (RuntimeException $e) {
                    $messages->addMessage(
                        'admin.error',
                        sprintf('Workshop feed could not be generated. Error: "%s"', $e->getMessage())
                    );
                }

                return $response
                    ->withStatus(302)
                    ->withHeader('Location', '/admin/workshops/all');
            }
        );
    })
    ->add($container->get(Authenticator::class))
    ->add(function (Request $request, Response $response, callable $next) {

        $renderer = $this->get(PhpRenderer::class);

        $request = $request
            ->withAttribute('user', $this->get(AuthenticationService::class));

        $renderer
            ->addAttribute('user', $this->get(AuthenticationService::class)->getIdentity());

        $renderer
            ->addAttribute('successMessages', $this->get(Messages::class)->getMessage('admin.success') ?? []);

        $renderer
            ->addAttribute('errorMessages', $this->get(Messages::class)->getMessage('admin.error') ?? []);

        return $next($request, $response);
    })
    ->add(AdminStyle::class);

$app
    ->map(['GET', 'POST'], '/login', Login::class)
    ->add(AdminStyle::class);

$app->get('/logout', function (AuthenticationService $auth, Response $response) {
    $auth->logout();

    return $response
        ->withStatus(302)
        ->withHeader('Location', '/');
});

// Run app
$app->run();

