<?php

use PhpSchool\Website\Action\Admin\ClearCache;
use PhpSchool\Website\Action\Admin\Event\All as AllEvents;
use PhpSchool\Website\Action\Admin\Event\Create as EventCreate;
use PhpSchool\Website\Action\Admin\Event\Delete as EventDelete;
use PhpSchool\Website\Action\Admin\Event\Update as EventUpdate;
use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Delete;
use PhpSchool\Website\Action\Admin\Workshop\Promote;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\Admin\Workshop\View;
use PhpSchool\Website\Action\ApiDocsAction;
use PhpSchool\Website\Action\DocsAction;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Cache;
use PhpSchool\Website\ContainerFactory;
use PhpSchool\Website\DocumentationAction;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Form\FormFactory;
use PhpSchool\Website\Middleware\AdminStyle;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\User\AuthenticationService;
use PhpSchool\Website\User\Middleware\Authenticator;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Slim\Flash\Messages;
use Jenssegers\Agent\Agent;

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

$container = (new ContainerFactory)();
$app = $container->get('app');
$app->get('/', function (Request $request, Response $response, PhpRenderer $renderer, WorkshopRepository $workshopRepository) {

    $workshops = $workshopRepository->findAllApproved();

    $core = array_filter($workshops, function (Workshop $workshop) {
        return $workshop->isCore();
    });

    $community = array_filter($workshops, function (Workshop $workshop) {
        return $workshop->isCommunity();
    });

    $inner = $renderer->fetch('home.phtml', ['coreWorkshops' => $core, 'communityWorkshops' => $community]);

    return $renderer->render(
        $response, 'layouts/layout.phtml', [
            'pageTitle' => 'Home',
            'pageDescription' => 'Learn PHP the right way... the open source way. PHP School Open Source Learning for PHP',
            'content' => $inner,
        ]
    );
});

$app->add(function (Request $request, Response $response, callable $next) {
    $renderer = $this->get(PhpRenderer::class);
    $renderer->addAttribute('userAgent', new Agent);

    return $next($request, $response);
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
$app->get('/submit', SubmitWorkshop::class . '::showSubmitForm');
$app->post('/submit', SubmitWorkshop::class . '::submit');

$app
    ->group('/admin', function () {

        $this->get('', function (Request $request, Response $response, PhpRenderer $renderer) {
            return $renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area',
                'pageDescription' => 'Admin Area',
                'content'         => $renderer->fetch('admin/main.phtml')
            ]);
        });

        $this->get('/cache/clear', ClearCache::class);

        $this->group('/workshop', function () {
            $this->get('/all', All::class);
            $this->get('/new', Requests::class);
            $this->get('/approve/{id}', Approve::class);
            $this->get('/promote/{id}', Promote::class);
            $this->get('/delete/{id}', Delete::class);
        });

        $this->group('/event', function () {
            $this->get('/all', AllEvents::class);
            $this->get('/create', EventCreate::class . '::showCreateForm');
            $this->post('/create', EventCreate::class . '::create');
            $this->get('/update/{id}', EventUpdate::class . '::showUpdateForm');
            $this->post('/update/{id}', EventUpdate::class . '::update');
            $this->get('/delete/{id}', EventDelete::class);
        });

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
                    ->withHeader('Location', '/admin/workshop/all');
            }
        );

        $this->get('/workshop/view/{id}', View::class);
    })
    ->add($container->get(Authenticator::class))
    ->add(function (Request $request, Response $response, callable $next) {

        $response = $response->withHeader('cache-control', 'no-cache');

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

$app->get('/login', Login::class . '::showLoginForm');
$app->post('/login', Login::class . '::login');
$app->get('/logout', function (AuthenticationService $auth, Response $response) {
    $auth->logout();

    return $response
        ->withStatus(302)
        ->withHeader('Location', '/');
});
$app->post('/downloads/{workshop}/{version}', TrackDownloads::class)->add(new \RKA\Middleware\IpAddress());

$app->get('/events', function (Request $request, Response $response, EventRepository $repository, PhpRenderer $renderer) {

    $previousEvents = $repository->findPrevious();
    $events = $repository->findUpcoming();

    $inner = $renderer->fetch('events.phtml', ['events' => $events, 'previousEvents' => $previousEvents]);
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Events',
        'pageDescription' => 'PHP School Events!',
        'content'         => $inner
    ]);
});

// Run app
$app->run();

