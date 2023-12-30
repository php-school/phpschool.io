<?php

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\Action\Admin\ClearCache;
use PhpSchool\Website\Action\Admin\Event\All as AllEvents;
use PhpSchool\Website\Action\Admin\Event\Create as EventCreate;
use PhpSchool\Website\Action\Admin\Event\Delete as EventDelete;
use PhpSchool\Website\Action\Admin\Event\Update as EventUpdate;
use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Delete;
use PhpSchool\Website\Action\Admin\Workshop\Promote;
use PhpSchool\Website\Action\Admin\Workshop\RegenerateFeed;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\Admin\Workshop\View;
use PhpSchool\Website\Action\BlogPost;
use PhpSchool\Website\Action\BlogPosts;
use PhpSchool\Website\Action\Events;
use PhpSchool\Website\Action\StudentLogin;
use PhpSchool\Website\Action\StudentLogout;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Cloud\Action\ComposerPackageAdd;
use PhpSchool\Website\Cloud\Action\ComposerPackageSearch;
use PhpSchool\Website\Cloud\Action\Dashboard;
use PhpSchool\Website\Cloud\Action\ExerciseEditor;
use PhpSchool\Website\Cloud\Action\ResetState;
use PhpSchool\Website\Cloud\Action\ResetStateFromEditor;
use PhpSchool\Website\Cloud\Action\RunExercise;
use PhpSchool\Website\Cloud\Action\TourComplete;
use PhpSchool\Website\Cloud\Action\VerifyExercise;
use PhpSchool\Website\Cloud\Middleware\ExerciseRunnerRateLimiter;
use PhpSchool\Website\Cloud\Middleware\Styles;
use PhpSchool\Website\ContainerFactory;
use PhpSchool\Website\Middleware\AdminStyle;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\FlashMessages;
use PhpSchool\Website\User\Middleware\AdminAuthenticator;
use PhpSchool\Website\User\Middleware\StudentAuthenticator;
use Psr\Http\Message\ResponseInterface as Response;
use PhpSchool\Website\PhpRenderer;
use Psr\Log\LoggerInterface;
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

$errors = $app->addErrorMiddleware(
    (bool) $container->get('settings.displayErrorDetails'),
    true,
    true,
    $container->get(LoggerInterface::class)
);

$app->post('/submit', SubmitWorkshop::class);
$app->get('/api/events', Events::class);
$app->get('/api/posts', BlogPosts::class);
$app->get('/api/post/{slug}', BlogPost::class);

$app
    ->group('/admin', function (RouteCollectorProxy $group) {

        $group->get('', function (Request $request, Response $response, PhpRenderer $renderer) {
            return $renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area',
                'pageDescription' => 'Admin Area',
            ]);
        });

        $group->post('/cache/clear', ClearCache::class);

        $group->group('/workshop', function (RouteCollectorProxy $group) {
            $group->get('/all', All::class);
            $group->get('/new', Requests::class);
            $group->post('/approve/{id}', Approve::class);
            $group->post('/promote/{id}', Promote::class);
            $group->delete('/delete/{id}', Delete::class);
            $group->get('/view/{id}', View::class);
            $group->post('/regenerate', RegenerateFeed::class);
        });

        $group->group('/event', function (RouteCollectorProxy $group) {
            $group->get('/all', AllEvents::class);
            $group->post('/create', EventCreate::class);
            $group->post('/update/{id}', EventUpdate::class);
            $group->delete('/delete/{id}', EventDelete::class);
        });

        $group->group('/student', function (RouteCollectorProxy $group) {
            $group->get('/all', function (Request $request, Response $response, EntityManagerInterface $entityManager) {
                $response
                    ->getBody()
                    ->write(json_encode($entityManager->getRepository(Student::class)->findAll()));

                return $response
                    ->withStatus(200)
                    ->withHeader('Content-Type', 'application/json');
            });
        });

        //fallback for vue routes
        //all gets should be handled by vue router
        $group->get('/{sth}', function (Request $request, Response $response, PhpRenderer $renderer) {
            return $renderer->render($response, 'layouts/admin.phtml', [
                'pageTitle'       => 'Admin Area',
                'pageDescription' => 'Admin Area',
            ]);
        });
    })
    ->add($container->get(AdminAuthenticator::class))
    ->add(function (Request $request, RequestHandler $handler): Response {
        $renderer = $this->get(PhpRenderer::class);

        $request = $request
            ->withAttribute('user', $this->get(AdminAuthenticationService::class));

        $renderer
            ->addAttribute('user', $this->get(AdminAuthenticationService::class)->getIdentity());

        $renderer
            ->addAttribute('successMessages', $this->get(FlashMessages::class)->getMessage('admin.success') ?? []);

        $renderer
            ->addAttribute('errorMessages', $this->get(FlashMessages::class)->getMessage('admin.error') ?? []);

        return $handler->handle($request)
            ->withHeader('cache-control', 'no-cache');
    })
    ->add(AdminStyle::class);

$app->get('/login', Login::class . '::showLoginForm');
$app->post('/login', Login::class . '::login');
$app->post('/logout', function (AdminAuthenticationService $auth, Response $response) {
    $auth->logout();

    return $response
        ->withStatus(302)
        ->withHeader('Location', '/');
});
$app->post('/downloads/{workshop}/{version}', TrackDownloads::class)->add(new \RKA\Middleware\IpAddress());

$app->get('/student-login', StudentLogin::class);
$app
    ->group('/online', function (RouteCollectorProxy $group) use ($container) {
        $rateLimiter = $container->get(ExerciseRunnerRateLimiter::class);

        $group->post('/reset', ResetState::class);
        $group->get('/logout', StudentLogout::class);
        $group->post('/workshop/{workshop}/exercise/{exercise}/reset', ResetStateFromEditor::class);

        $group->get('/dashboard', Dashboard::class)
            ->setName('dashboard');

        $group->get('/workshop/{workshop}/exercise/{exercise}/editor', ExerciseEditor::class);
        $group->post('/workshop/{workshop}/exercise/{exercise}/run', RunExercise::class)->add($rateLimiter);
        $group->post('/workshop/{workshop}/exercise/{exercise}/verify', VerifyExercise::class)->add($rateLimiter);
        $group->get('/composer-package/add', ComposerPackageAdd::class);
        $group->get('/composer-package/search', ComposerPackageSearch::class);
        $group->post('/tour/complete', TourComplete::class);
    })
    ->add($container->get(StudentAuthenticator::class))
    ->add(Styles::class);

// Run app
$app->run();
