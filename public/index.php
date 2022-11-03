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
use PhpSchool\Website\Action\DocsAction;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Cloud\Action\ListWorkshops;
use PhpSchool\Website\Cloud\Middleware\Styles;
use PhpSchool\Website\ContainerFactory;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Middleware\AdminStyle;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\User\AuthenticationService;
use PhpSchool\Website\User\Middleware\Authenticator;
use PhpSchool\Website\WorkshopFeed;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Psr\Log\LoggerInterface;
use Slim\Flash\Messages;
use Jenssegers\Agent\Agent;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

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

/** @var \Slim\App $app */
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

$app->add(function (Request $request, RequestHandler $handler) {
    $renderer = $this->get(PhpRenderer::class);
    $renderer->addAttribute('userAgent', new Agent);
    $renderer->addAttribute('route', $request->getUri()->getPath());

    return $handler->handle($request);
});

$errors = $app->addErrorMiddleware(
    (bool) $container->get('settings.displayErrorDetails'),
    true,
    true,
    $container->get(LoggerInterface::class)
);

$app->get('/install', function (Request $request, Response $response, PhpRenderer $renderer) {
    $inner = $renderer->fetch('install.phtml');
    return $renderer->render($response, 'layouts/layout.phtml', [
        'pageTitle'       => 'Installation instructions',
        'pageDescription' => 'Installation instructions for PHPSchool',
        'content'         => $inner
    ]);
});

$app->get('/docs[/{group}[/{section}]]', DocsAction::class);
$app->get('/submit', SubmitWorkshop::class . '::showSubmitForm');
$app->post('/submit', SubmitWorkshop::class . '::submit');

$app
    ->group('/admin', function (RouteCollectorProxy $group) {

        $group->get('', function (Request $request, Response $response, PhpRenderer $renderer) {
            return $renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area',
                'pageDescription' => 'Admin Area',
                'content'         => $renderer->fetch('admin/main.phtml')
            ]);
        });

        $group->get('/cache/clear', ClearCache::class);

        $group->group('/workshop', function (RouteCollectorProxy $group) {
            $group->get('/all', All::class);
            $group->get('/new', Requests::class);
            $group->get('/approve/{id}', Approve::class);
            $group->get('/promote/{id}', Promote::class);
            $group->get('/delete/{id}', Delete::class);
        });

        $group->group('/event', function (RouteCollectorProxy $group) {
            $group->get('/all', AllEvents::class);
            $group->get('/create', EventCreate::class . '::showCreateForm');
            $group->post('/create', EventCreate::class . '::create');
            $group->get('/update/{id}', EventUpdate::class . '::showUpdateForm');
            $group->post('/update/{id}', EventUpdate::class . '::update');
            $group->get('/delete/{id}', EventDelete::class);
        });

        $group->get(
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

        $group->get('/workshop/view/{id}', View::class);
    })
    ->add($container->get(Authenticator::class))
    ->add(function (Request $request, RequestHandler $handler): Response {
        $renderer = $this->get(PhpRenderer::class);

        $request = $request
            ->withAttribute('user', $this->get(AuthenticationService::class));

        $renderer
            ->addAttribute('user', $this->get(AuthenticationService::class)->getIdentity());

        $renderer
            ->addAttribute('successMessages', $this->get(Messages::class)->getMessage('admin.success') ?? []);

        $renderer
            ->addAttribute('errorMessages', $this->get(Messages::class)->getMessage('admin.error') ?? []);

        return $handler->handle($request)
            ->withHeader('cache-control', 'no-cache');
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

$app
    ->group('/cloud', function (RouteCollectorProxy $group) {
        $group->get('', ListWorkshops::class);
    })
    ->add(Styles::class);

// Run app
$app->run();