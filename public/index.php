<?php

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\Action\Admin\ClearCache;
use PhpSchool\Website\Action\Admin\Event\All as AllEvents;
use PhpSchool\Website\Action\Admin\Event\Create as EventCreate;
use PhpSchool\Website\Action\Admin\Event\Delete as EventDelete;
use PhpSchool\Website\Action\Admin\Event\Update as EventUpdate;
use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Delete;
use PhpSchool\Website\Action\Admin\Workshop\Promote;
use PhpSchool\Website\Action\Admin\Workshop\RegenerateFeed;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\View;
use PhpSchool\Website\Action\BlogPost;
use PhpSchool\Website\Action\BlogPosts;
use PhpSchool\Website\Action\Contributors;
use PhpSchool\Website\Action\Events;
use PhpSchool\Website\Action\Online\ComposerPackageAdd;
use PhpSchool\Website\Action\Online\ComposerPackageSearch;
use PhpSchool\Website\Action\Online\WorkshopExercise;
use PhpSchool\Website\Action\Online\ResetState;
use PhpSchool\Website\Action\Online\RunExercise;
use PhpSchool\Website\Action\Online\TourComplete;
use PhpSchool\Website\Action\Online\VerifyExercise;
use PhpSchool\Website\Action\Online\Workshops;
use PhpSchool\Website\Action\SlackInvite;
use PhpSchool\Website\Action\StudentLogin;
use PhpSchool\Website\Action\StudentLogout;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Online\Middleware\ExerciseRunnerRateLimiter;
use PhpSchool\Website\Online\Middleware\Styles;
use PhpSchool\Website\ContainerFactory;
use PhpSchool\Website\Middleware\IpRateLimiter;
use RKA\Middleware\IpAddress;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Middleware\RequireLogin;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Routing\RouteCollectorProxy;
use JimTools\JwtAuth\Exceptions\AuthorizationException;
use JimTools\JwtAuth\Middleware\JwtAuthentication;

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
$app->addBodyParsingMiddleware();

$errors = $app->addErrorMiddleware(
    (bool) $container->get('settings.displayErrorDetails'),
    true,
    true,
    $container->get(LoggerInterface::class)
);
$errors->setErrorHandler(
    AuthorizationException::class,
    static fn (Request $request) => $app->getResponseFactory()->createResponse(401)
);
// every route is an API, so errors are always JSON, whatever the request's Accept header says,
// and in the same { "error": "..." } shape the actions use, e.g. { "error": "Not found." }
$errors->getDefaultErrorHandler()->forceContentType('application/json');
$errors->getDefaultErrorHandler()->registerErrorRenderer(
    'application/json',
    static fn (Throwable $e, bool $displayErrorDetails) => json_encode([
        'error' => $e instanceof HttpException || $displayErrorDetails ? $e->getMessage() : 'Something went wrong',
    ])
);

// the site is behind Cloudflare, so the visitor's IP is in CF-Connecting-IP, not REMOTE_ADDR
$ipAddress = new IpAddress(true, [], 'ip_address', ['CF-Connecting-IP']);

// eg $limitPerIp('adminLogin', 5, '1 minute'), for public routes that anyone could hammer
$limitPerIp = fn (string $id, int $limit, string $interval) => new IpRateLimiter(new RateLimiterFactory(
    ['id' => $id, 'policy' => 'sliding_window', 'limit' => $limit, 'interval' => $interval],
    $container->get('rateLimiterStorage'),
));

$app->post('/api/submit', SubmitWorkshop::class)->add($container->get(RequireLogin::class));
$app->get('/api/events', Events::class);
$app->get('/api/posts', BlogPosts::class);
// middleware runs last added first, so $ipAddress sets the IP before the limiter reads it
$app->post('/api/slack-invite', SlackInvite::class)->add($limitPerIp('slackInvite', 5, '1 hour'))->add($ipAddress);
$app->get('/api/contributors', Contributors::class);
$app->post('/api/admin/login', Login::class)->add($limitPerIp('adminLogin', 5, '1 minute'))->add($ipAddress);

$app
    ->group('/api/admin', function (RouteCollectorProxy $group) {

        $group->post('/cache/clear', ClearCache::class);

        $group->group('/workshop', function (RouteCollectorProxy $group) {
            $group->get('/all', All::class);
            $group->get('/new', Requests::class);
            $group->post('/approve/{id:[0-9a-f-]{36}}', Approve::class);
            $group->post('/promote/{id:[0-9a-f-]{36}}', Promote::class);
            $group->delete('/delete/{id:[0-9a-f-]{36}}', Delete::class);
            $group->get('/view/{id:[0-9a-f-]{36}}', View::class);
            $group->post('/regenerate', RegenerateFeed::class);
        });

        $group->group('/event', function (RouteCollectorProxy $group) {
            $group->get('/all', AllEvents::class);
            $group->post('/create', EventCreate::class);
            $group->post('/update/{id:[0-9a-f-]{36}}', EventUpdate::class);
            $group->delete('/delete/{id:[0-9a-f-]{36}}', EventDelete::class);
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
    })
    ->add($container->get(JwtAuthentication::class));

$app->post('/downloads/{workshop}/{version}', TrackDownloads::class)
    ->add($limitPerIp('trackDownloads', 30, '1 minute'))
    ->add($ipAddress);

$rateLimiter = $container->get(ExerciseRunnerRateLimiter::class);

$app->get('/api/online/student/login-url', [StudentLogin::class, 'redirectUrl']);
$app->get('/api/online/student/login', StudentLogin::class);
$app->post('/api/online/student/logout', StudentLogout::class);
$app->get('/api/online/workshops', Workshops::class);

$app
    ->group('/api/online', function (RouteCollectorProxy $group) use ($container, $rateLimiter) {

        $group->get('/student', [StudentLogin::class, 'getStudent']);
        $group->get('/workshop/{workshop}/exercise/{exercise}', WorkshopExercise::class);
        $group->post('/workshop/run/{workshop}/exercise/{exercise}', RunExercise::class)->add($rateLimiter);
        $group->post('/workshop/verify/{workshop}/exercise/{exercise}', VerifyExercise::class)->add($rateLimiter);
        $group->get('/composer-package/add', ComposerPackageAdd::class);
        $group->get('/composer-package/search', ComposerPackageSearch::class);
        $group->post('/tour/complete', TourComplete::class);
        $group->post('/reset', ResetState::class);
    })
    ->add($container->get(RequireLogin::class));

// Run app
$app->run();
