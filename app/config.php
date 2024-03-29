<?php

declare(strict_types=1);

use ahinkle\PackagistLatestVersion\PackagistLatestVersion;
use DI\Bridge\Slim\Bridge;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Github\AuthMethod;
use Github\Client;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;
use League\OAuth2\Client\Provider\Github;
use Mni\FrontYAML\Parser;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpSchool\PhpWorkshop\Markdown\CurrentContext;
use PhpSchool\PhpWorkshop\Markdown\ProblemFileExtension;
use PhpSchool\PhpWorkshop\Markdown\Renderer\ContextSpecificRenderer;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\AppName;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\Run;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\Verify;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Context;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Documentation as DocumentationShorthand;
use PhpSchool\Website\Action\Admin\ClearCache as ClearCacheAction;
use PhpSchool\Website\Action\Admin\Event\All as EventAll;
use PhpSchool\Website\Action\Admin\Event\Create as EventCreate;
use PhpSchool\Website\Action\Admin\Event\Delete as EventDelete;
use PhpSchool\Website\Action\Admin\Event\Update as EventUpdate;
use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Delete;
use PhpSchool\Website\Action\Admin\Workshop\Promote;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\View;
use PhpSchool\Website\Action\Online\ComposerPackageAdd;
use PhpSchool\Website\Action\Online\RunExercise;
use PhpSchool\Website\Action\Online\VerifyExercise;
use PhpSchool\Website\Action\SlackInvite;
use PhpSchool\Website\Action\StudentLogin;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Blog\Generator;
use PhpSchool\Website\Command\SyncContributors;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\Command\DownloadComposerPackageList;
use PhpSchool\Website\Online\Middleware\ExerciseRunnerRateLimiter;
use PhpSchool\Website\Online\PathGenerator;
use PhpSchool\Website\Online\ProblemFileConverter;
use PhpSchool\Website\Online\ProjectUploader;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\Online\VueResultsRenderer;
use PhpSchool\Website\Command\ClearCache;
use PhpSchool\Website\Command\CreateAdminUser;
use PhpSchool\Website\Command\GenerateBlog;
use PhpSchool\Website\Entity\BlogPost;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\Form\FormHandlerFactory;
use PhpSchool\Website\InputFilter\Event as EventInputFilter;
use PhpSchool\Website\InputFilter\Login as LoginInputFilter;
use PhpSchool\Website\InputFilter\SubmitWorkshop as SubmitWorkshopInputFilter;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;
use PhpSchool\Website\Middleware\Session as SessionMiddleware;
use PhpSchool\Website\Repository\DoctrineORMBlogRepository;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\User\Adapter\Doctrine;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Middleware\StudentAuthenticator;
use PhpSchool\Website\User\Middleware\StudentRefresher;
use PhpSchool\Website\User\Session;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentRepository;
use PhpSchool\Website\Workshop\EmailNotifier;
use PhpSchool\Website\WorkshopFeed;
use Predis\Connection\ConnectionException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Doctrine\UuidType;
use Slim\App;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Contracts\Cache\CacheInterface;
use Tuupola\Middleware\JwtAuthentication;

use function DI\factory;
use function DI\get;

return [
    'console' => factory(function (DI\Container $c): Silly\Edition\PhpDi\Application {
        $app = new Silly\Edition\PhpDi\Application('PHP School Website', 'UNKNOWN', $c);
        $app->command('clear-cache', ClearCache::class);
        $app->command('create-admin-user name email password', CreateAdminUser::class);
        $app->command('generate-blog', GenerateBlog::class);
        $app->command('download-composer-packages', DownloadComposerPackageList::class);
        $app->command('sync-contributors', SyncContributors::class);

        ConsoleRunner::addCommands($app, new SingleManagerProvider($c->get(EntityManagerInterface::class)));

        return $app;
    }),
    'basePath' => __DIR__ . '/../',
    'app' => factory(function (ContainerInterface $c): App {
        $app =  Bridge::create($c);
        $app->addRoutingMiddleware();

        $app->add(function (Request $request, RequestHandler $handler) use ($c): Response {
            /** @var Session $session */
            $session  = $this->get(Session::class);

            $student = $session->get('student');

            $request = $request->withAttribute('student', $student);

            return $handler->handle($request)
                ->withHeader('cache-control', 'no-cache');
        });
        $app->add(StudentRefresher::class);
        $app->add(new SessionMiddleware(['name' => 'phpschool']));

        return $app;
    }),
    'cache' => factory(function (ContainerInterface $c): CacheInterface {
        if (!$c->get('config')['enableCache']) {
            return new NullAdapter();
        }

        $redisConnection = new \Predis\Client(['host' => $c->get('config')['redisHost']]);
        try {
            $redisConnection->connect();
        } catch (ConnectionException $e) {
            throw new \RuntimeException(
                sprintf(
                    'Could not connect to redis using host: "%s". Message: "%s"',
                    $c->get('config')['redisHost'],
                    $e->getMessage()
                )
            );
        }

        return new RedisAdapter($redisConnection, 'default');
    }),
    LoggerInterface::class => factory(function (ContainerInterface $c): LoggerInterface {
        $settings = $c->get('config')['logger'];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor());
        $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
        return $logger;
    }),

    SessionStorageInterface::class => get(Session::class),

    Session::class => function (ContainerInterface $c): Session {
        return new Session();
    },

    FormHandlerFactory::class => function (ContainerInterface $c): FormHandlerFactory {
        return new FormHandlerFactory($c->get(Session::class));
    },

    //commands
    ClearCache::class => factory(function (ContainerInterface $c): ClearCache {
        return new ClearCache($c->get('cache'));
    }),
    CreateAdminUser::class => factory(function (ContainerInterface $c): CreateAdminUser {
        return new CreateAdminUser($c->get(EntityManagerInterface::class));
    }),
    GenerateBlog::class => function (ContainerInterface $c): GenerateBlog {
        return new GenerateBlog($c->get(Generator::class));
    },
    DownloadComposerPackageList::class => function (ContainerInterface $c): DownloadComposerPackageList {
        return new DownloadComposerPackageList($c->get('guzzle.packagist'), $c->get(LoggerInterface::class));
    },
    SyncContributors::class => function (ContainerInterface $c): SyncContributors {
        return new SyncContributors($c->get(Client::class), $c->get(LoggerInterface::class));
    },

    TrackDownloads::class => function (ContainerInterface $c): TrackDownloads {
        return new TrackDownloads($c->get(WorkshopRepository::class), $c->get(WorkshopInstallRepository::class));
    },

    SubmitWorkshop::class => \DI\factory(function (ContainerInterface $c): SubmitWorkshop {
        return new SubmitWorkshop(
            $c->get(FormHandlerFactory::class)->create(
                new SubmitWorkshopInputFilter(new Client(), $c->get(WorkshopRepository::class))
            ),
            new WorkshopCreator(new WorkshopComposerJsonInputFilter(), $c->get(WorkshopRepository::class)),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

    SlackInvite::class => function (ContainerInterface $c): SlackInvite {
        return new SlackInvite(
            $c->get('guzzle'),
            $c->get('config')['slackInviteApiToken']
        );
    },

    Client::class => function (ContainerInterface $c): Client {
        $client = new Client();
        $client->authenticate($c->get('config')['github']['token'], AuthMethod::ACCESS_TOKEN);

        return $client;
    },

    Github::class => function (ContainerInterface $c): Github {
        return new Github([
            'clientId' => $c->get('config')['github']['clientId'],
            'clientSecret' => $c->get('config')['github']['clientSecret'],
        ]);
    },

    StudentLogin::class => function (ContainerInterface $c): StudentLogin {
        return new StudentLogin(
            $c->get(Github::class),
            $c->get(Session::class),
            $c->get(EntityManagerInterface::class)
        );
    },

    //admin
    Login::class => \DI\factory(function (ContainerInterface $c): Login {
        return new Login(
            $c->get(AdminAuthenticationService::class),
            $c->get(FormHandlerFactory::class)->create(new LoginInputFilter()),
            $c->get('config')['jwtSecret']
        );
    }),

    ClearCacheAction::class => function (ContainerInterface $c): ClearCacheAction {
        return new ClearCacheAction(
            $c->get('cache'),
        );
    },

    Requests::class => \DI\factory(function (ContainerInterface $c): Requests {
        return new Requests(
            $c->get(WorkshopRepository::class),
        );
    }),

    All::class => \DI\factory(function (ContainerInterface $c): All {
        return new All(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
        );
    }),

    Approve::class => \DI\factory(function (ContainerInterface $c): Approve {
        return new Approve(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache'),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

    Promote::class => \DI\factory(function (ContainerInterface $c): Promote {
        return new Promote(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache'),
        );
    }),

    Delete::class => \DI\factory(function (ContainerInterface $c): Delete {
        return new Delete(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache'),
        );
    }),

    View::class => function (ContainerInterface $c): View {
        return new View(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
        );
    },

    'guzzle' => function (ContainerInterface $c): \GuzzleHttp\Client {
        return new \GuzzleHttp\Client();
    },

    'guzzle.packagist' => function (ContainerInterface $c) {
        return new \GuzzleHttp\Client(['headers' => ['User-Agent' => 'PHP School: phpschool.team@gmail.com']]);
    },

    ComposerPackageAdd::class => function (ContainerInterface $c): ComposerPackageAdd {
        return new ComposerPackageAdd(
            new PackagistLatestVersion($c->get('guzzle.packagist')),
        );
    },

    CurrentContext::class => function (): CurrentContext {
        return CurrentContext::cloud();
    },

    //cloud
    MarkdownConverterInterface::class => function (ContainerInterface $c): MarkdownConverterInterface {
        $environment = new \League\CommonMark\Environment([
            'external_link' => [
                'internal_hosts' => 'www.phpschool.io',
                'open_in_new_window' => true,
                'nofollow' => '',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new ExternalLinkExtension());

        $environment
            ->addExtension(new ProblemFileExtension(
                $c->get(ContextSpecificRenderer::class),
                [
                    new AppName(),
                    new DocumentationShorthand(),
                    new Run(),
                    new Verify(),
                    $c->get(Context::class)
                ]
            ));

        return new MarkdownConverter($environment);
    },

    ProblemFileConverter::class => function (ContainerInterface $c): ProblemFileConverter {
        return new ProblemFileConverter($c->get(MarkdownConverterInterface::class));
    },

    RunExercise::class => function (ContainerInterface $c): RunExercise {
        return new RunExercise(
            $c->get(CloudWorkshopRepository::class),
            $c->get(ProjectUploader::class),
            $c->get(StudentWorkshopState::class),
            $c->get(SessionStorageInterface::class),
        );
    },

    VerifyExercise::class => function (ContainerInterface $c): VerifyExercise {
        return new VerifyExercise(
            $c->get(CloudWorkshopRepository::class),
            $c->get(ProjectUploader::class),
            $c->get(SessionStorageInterface::class),
            $c->get(StudentWorkshopState::class),
            new VueResultsRenderer()
        );
    },

    ProjectUploader::class => function (ContainerInterface $c): ProjectUploader {
        return new ProjectUploader(new PathGenerator());
    },

    'form.event' => function (ContainerInterface $c): FormHandler {
        return $c->get(FormHandlerFactory::class)->create(new EventInputFilter());
    },

    EventAll::class => function (ContainerInterface $c): EventAll {
        return new EventAll($c->get(EventRepository::class));
    },

    EventCreate::class => function (ContainerInterface $c): EventCreate {
        return new EventCreate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
        );
    },

    EventUpdate::class => function (ContainerInterface $c): EventUpdate {
        return new EventUpdate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
        );
    },

    EventDelete::class => function (ContainerInterface $c): EventDelete {
        return new EventDelete(
            $c->get(EventRepository::class),
            $c->get('cache'),
        );
    },

    WorkshopFeed::class => \DI\factory(function (ContainerInterface $c): WorkshopFeed {
        return new WorkshopFeed(
            $c->get(WorkshopRepository::class),
            __DIR__ . '/../public/workshops.json'
        );
    }),

    WorkshopRepository::class => \DI\factory(function (ContainerInterface $c): WorkshopRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(Workshop::class);
    }),

    WorkshopInstallRepository::class => \DI\factory(function (ContainerInterface $c): WorkshopInstallRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(WorkshopInstall::class);
    }),

    EventRepository::class => function (ContainerInterface $c): EventRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(Event::class);
    },

    DoctrineORMBlogRepository::class => function (ContainerInterface $c): DoctrineORMBlogRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(BlogPost::class);
    },

    StudentRepository::class => function (ContainerInterface $c): StudentRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(Student::class);
    },

    AdminAuthenticationService::class => \DI\factory(function (ContainerInterface $c): AdminAuthenticationService {
        $authService = new \Laminas\Authentication\AuthenticationService(
            new \Laminas\Authentication\Storage\NonPersistent(),
            new Doctrine($c->get(EntityManagerInterface::class))
        );
        return new AdminAuthenticationService($authService);
    }),

    StudentAuthenticator::class => function (ContainerInterface $c): StudentAuthenticator {
        return new StudentAuthenticator(
            $c->get(Session::class),
        );
    },

    ORMSetup::class => \DI\factory(function (ContainerInterface $c): Configuration {
        $doctrineConfig = $c->get('config')['doctrine'];

        $config = ORMSetup::createAnnotationMetadataConfiguration(
            $doctrineConfig['meta']['entity_path'],
            $doctrineConfig['meta']['auto_generate_proxies'],
            $doctrineConfig['meta']['proxy_dir'],
        );

        $config->setMetadataCache($c->get('cache'));
        $config->setQueryCache($c->get('cache'));
        $config->setHydrationCache($c->get('cache'));
        $config->setResultCache($c->get('cache'));

        return $config;
    }),

    EntityManagerInterface::class => \DI\factory(function (ContainerInterface $c): EntityManagerInterface {
        Type::addType('uuid', UuidType::class);


        $driver = \Doctrine\DBAL\DriverManager::getConnection(
            $c->get('config')['doctrine']['connection'],
        );

        return new EntityManager(
            $driver,
            $c->get(ORMSetup::class)
        );
    }),

    EmailNotifier::class => function (ContainerInterface $c): EmailNotifier {
        return new EmailNotifier(
            new \SendGrid((string) getenv('SEND_GRID_API_KEY')),
            (string) getenv('SEND_GRID_SENDER_EMAIL')
        );
    },

    Generator::class => function (ContainerInterface $c): Generator {
        return new Generator(
            new Parser(null, new class () implements \Mni\FrontYAML\Markdown\MarkdownParser {
                public function parse($markdown): string
                {
                    return (new Parsedown())->parse($markdown);
                }
            }),
            $c->get(DoctrineORMBlogRepository::class),
            __DIR__ . '/../posts/',
        );
    },

    CloudWorkshopRepository::class => function (ContainerInterface $c): CloudWorkshopRepository {
        return new CloudWorkshopRepository($c->get(WorkshopRepository::class));
    },

    'exerciseRunnerRateLimiterFactory' => function (ContainerInterface $c): RateLimiterFactory {
        $redisConnection = new \Predis\Client(['host' => $c->get('config')['redisHost']]);
        try {
            $redisConnection->connect();
        } catch (ConnectionException $e) {
            throw new \RuntimeException(
                sprintf(
                    'Could not connect to redis using host: "%s". Message: "%s"',
                    $c->get('config')['redisHost'],
                    $e->getMessage()
                )
            );
        }

        $adapter = new RedisAdapter($redisConnection, 'rate_limiter');

        return new RateLimiterFactory(
            [
                'id' => 'exerciseRunner',
                'policy' => 'sliding_window',
                'limit' => 10,
                'interval' => '1 minute',
            ],
            new CacheStorage($adapter)
        );
    },

    ExerciseRunnerRateLimiter::class => function (ContainerInterface $c): ExerciseRunnerRateLimiter {
        return new ExerciseRunnerRateLimiter(
            $c->get(SessionStorageInterface::class),
            $c->get('exerciseRunnerRateLimiterFactory')
        );
    },

    JwtAuthentication::class => function (ContainerInterface $c): JwtAuthentication {
        return new JwtAuthentication([
            'secret' => $c->get('config')['jwtSecret'],
            'path' => '/api/admin',
            "ignore" => ["/api/admin/login"],
            "secure" => !$c->get('config')['devMode'],
        ]);
    },

    'config' => [
        'containerCacheDir' => __DIR__ . '/../var/container_cache',

        'determineRouteBeforeAppMiddleware' => true,
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../var/logs/app.log',
        ],

        'links' => [
            'github'         => 'https://github.com/php-school/learn-you-php',
            'twitter'        => 'https://twitter.com/PHPSchoolTeam',
            'slack'          => 'https://phpschool.herokuapp.com',
            'discussions'    => 'https://github.com/php-school/discussions',
            'workshop'       => 'https://github.com/php-school/php-workshop',
            'github-website' => 'https://github.com/php-school/phpschool.io',
        ],

        'enableCache'       => filter_var($_ENV['CACHE.ENABLE'], FILTER_VALIDATE_BOOLEAN),
        'redisHost'         => $_ENV['REDIS_HOST'],
        'devMode'           => filter_var($_ENV['DEV_MODE'], FILTER_VALIDATE_BOOLEAN),

        'doctrine' => [
            'meta' => [
                'entity_path' => [
                    'src/Entity',
                    'src/User/Entity',
                ],
                'auto_generate_proxies' => true,
                'proxy_dir' =>  __DIR__ . '/../cache/proxies',
            ],
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => $_ENV['MYSQL_HOST'],
                'dbname'   => $_ENV['MYSQL_DATABASE'],
                'user'     => $_ENV['MYSQL_USER'],
                'password' => $_ENV['MYSQL_PASSWORD'],
                'charset' => 'utf8mb4',
            ]
        ],

        'github' => [
            'clientId' => $_ENV['GITHUB_CLIENT_ID'],
            'clientSecret' => $_ENV['GITHUB_CLIENT_SECRET'],
            'token' => $_ENV['GITHUB_TOKEN'],
        ],

        'jwtSecret' => $_ENV['JWT_SECRET'],
        'slackInviteApiToken' => $_ENV['SLACK_INVITE_API_TOKEN'],
    ],

    //slim settings
    'settings.displayErrorDetails' => filter_var($_ENV['DISPLAY_ERRORS'], FILTER_VALIDATE_BOOLEAN),
 ];
