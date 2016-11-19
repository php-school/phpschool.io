<?php

use function DI\factory;
use Doctrine\Common\Cache\Cache as DoctrineCache;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Setup;
use Github\Client;
use Interop\Container\ContainerInterface;
use League\CommonMark\CommonMarkConverter;
use Mni\FrontYAML\Parser;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpSchool\Website\Action\Admin\ClearCache as ClearCacheAction;
use PhpSchool\Website\Action\Admin\Event\All as EventAll;
use PhpSchool\Website\Action\Admin\Event\Create as EventCreate;
use PhpSchool\Website\Action\Admin\Event\Update as EventUpdate;
use PhpSchool\Website\Action\Admin\Event\Delete as EventDelete;
use PhpSchool\Website\Action\Admin\Login;
use PhpSchool\Website\Action\Admin\Workshop\Approve;
use PhpSchool\Website\Action\Admin\Workshop\Delete;
use PhpSchool\Website\Action\Admin\Workshop\Promote;
use PhpSchool\Website\Action\Admin\Workshop\Requests;
use PhpSchool\Website\Action\Admin\Workshop\All;
use PhpSchool\Website\Action\Admin\Workshop\View;
use PhpSchool\Website\Action\DocsAction;
use PhpSchool\Website\Action\ApiDocsAction;
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Blog\Generate;
use PhpSchool\Website\Blog\Generator;
use PhpSchool\Website\Cache;
use PhpSchool\Website\Command\ClearCache;
use PhpSchool\Website\Command\CreateUser;
use PhpSchool\Website\Command\GenerateBlog;
use PhpSchool\Website\Command\GenerateDoc;
use PhpSchool\Website\DocGenerator;
use PhpSchool\Website\Documentation;
use PhpSchool\Website\DocumentationGroup;
use PhpSchool\Website\DownloadManager;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Form\FormFactory;
use PhpSchool\Website\Form\FormHandlerFactory;
use PhpSchool\Website\InputFilter\SubmitWorkshop as SubmitWorkshopInputFilter;
use PhpSchool\Website\Middleware\FpcCache;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\User\Adapter\Doctrine;
use PhpSchool\Website\User\AuthenticationService;
use PhpSchool\Website\User\Middleware\Authenticator;
use PhpSchool\Website\InputFilter\Event as EventInputFilter;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;
use PhpSchool\Website\InputFilter\Login as LoginInputFilter;
use PhpSchool\Website\Workshop\EmailNotifier;
use PhpSchool\Website\WorkshopFeed;
use Psr\Log\LoggerInterface;
use PhpSchool\Website\PhpRenderer;
use Ramsey\Uuid\Doctrine\UuidType;
use RKA\Session;
use RKA\SessionMiddleware;
use Slim\Flash\Messages;
use Slim\Router;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;

return [
    'console' => factory(function (ContainerInterface $c) {
        $app = new Silly\Edition\PhpDi\Application('PHP School Website', null, $c);
        $app->command('generate-docs', GenerateDoc::class);
        $app->command('clear-cache', ClearCache::class);
        $app->command('create-user name email password', CreateUser::class);
        $app->command('generate-blog', GenerateBlog::class);
        return $app;
    }),
    'app' => factory(function (ContainerInterface $c) {
        $app = new \Slim\App($c);
        $app->add($c->get(FpcCache::class));
        $app->add(new SessionMiddleware(['name' => 'phpschool']));

        return $app;
    }),
    'router' => function (ContainerInterface $c) {
        $router = new Router;

        if ($c->get('config')['enableCache']) {
            if (!file_exists('../var/cache')) {
                mkdir('../var/cache', 0777, true);
            }

            $router->setCacheFile('../var/cache/router.php');
        }

        return $router;
    },
    FpcCache::class => factory(function (ContainerInterface $c) {
        return new FpcCache($c->get('cache.fpc'));
    }),
    'cache.fpc' => factory(function (ContainerInterface $c) {
        if (!$c->get('config')['enablePageCache']) {
            return new NullAdapter;
        }
        return new RedisAdapter(new Predis\Client(['host' => $c->get('config')['redisHost']]), 'fpc');
    }),
    PhpRenderer::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['renderer'];

        $renderer = new PhpRenderer(
            new CommonMarkConverter,
            $settings['template_path'],
            [
                'links'     => $c->get('config')['links'],
                'route'     => $c->get('request')->getUri()->getPath(),
            ]
        );

        //default CSS
        $renderer->appendLocalCss('main-css', __DIR__ . '/../public/css/core.css');
        $renderer->appendRemoteCss('font', 'https://fonts.googleapis.com/css?family=Open+Sans: 400,700');

        //default JS
        $renderer->addJs('jquery', '//code.jquery.com/jquery-1.12.0.min.js');
        $renderer->addJs('main-js', '/js/dist/main.min.js');

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

    Session::class => function (ContainerInterface $c) {
        return new Session;
    },

    FormHandlerFactory::class => function (ContainerInterface $c) {
        return new FormHandlerFactory($c->get(Session::class));
    },

    //commands
    GenerateDoc::class => factory(function (ContainerInterface $c) {
        return new GenerateDoc($c->get(DocGenerator::class), $c->get('cache'));
    }),
    ClearCache::class => factory(function (ContainerInterface $c) {
        return new ClearCache($c->get('cache.fpc'));
    }),
    CreateUser::class => factory(function (ContainerInterface $c) {
       return new CreateUser($c->get(EntityManagerInterface::class));
    }),
    GenerateBlog::class => function (ContainerInterface $c) {
        return new GenerateBlog($c->get(Generator::class));
    },

    Documentation::class => \DI\factory(function (ContainerInterface $c) {
        $tutorialGroup = new DocumentationGroup('tutorial', 'Workshop Tutorial');
        $tutorialGroup->addSection('index', 'Workshop Tutorial', 'docs/tutorial/index.phtml');
        $tutorialGroup->addSection('creating-your-own-workshop', 'Creating your own workshop', 'docs/tutorial/creating-your-own-workshop.phtml');
        $tutorialGroup->addSection('modify-theme', 'Modifying the theme of your workshop', 'docs/tutorial/modify-theme.phtml');
        $tutorialGroup->addSection('creating-an-exercise', 'Creating an exercise', 'docs/tutorial/creating-an-exercise.phtml');

        $referenceGroup = new DocumentationGroup('reference', 'Reference Documentation');
        $referenceGroup->addSection('index', 'Reference Documentation', 'docs/reference/index.phtml');
        $referenceGroup->addSection('container', 'The Container', 'docs/reference/container.phtml');
        $referenceGroup->addSection('available-services', 'Available Services', 'docs/reference/available-services.phtml');
        $referenceGroup->addSection('exercise-types', 'Exercise Types', 'docs/reference/exercise-types.phtml');
        $referenceGroup->addSection('exercise-solutions', 'Exercise Solutions', 'docs/reference/exercise-solutions.phtml');
        $referenceGroup->addSection('results', 'Results & Renderers', 'docs/reference/results.phtml');
        $referenceGroup->addSection('exercise-checks', 'Exercise Checks', 'docs/reference/exercise-checks.phtml');
        $referenceGroup->addSection('bundled-checks', 'Bundled Checks', 'docs/reference/bundled-checks.phtml');
        $referenceGroup->addSection('creating-simple-checks', 'Creating Simple Checks', 'docs/reference/creating-simple-checks.phtml');
        $referenceGroup->addSection('creating-custom-results', 'Creating Custom Results', 'docs/reference/creating-custom-results.phtml');
        $referenceGroup->addSection('creating-custom-result-renderers', 'Creating Custom Result Renderers', 'docs/reference/creating-custom-result-renderers.phtml');
        $referenceGroup->addSection('events', 'Events', 'docs/reference/events.phtml');
        $referenceGroup->addSection('creating-listener-checks', 'Creating Listener Checks', 'docs/reference/creating-listener-checks.phtml');
        $referenceGroup->addSection('self-checking-exercises', 'Self Checking Exercises', 'docs/reference/self-checking-exercises.phtml');
        $referenceGroup->addSection('exercise-events', 'Exercise Events', 'docs/reference/exercise-events.phtml');
        $referenceGroup->addSection('patching-exercise-solutions', 'Patching Exercise Submissions', 'docs/reference/patching-exercise-solutions.phtml');

        $apiGroup = new DocumentationGroup('api', 'API');
        $apiGroup->addExternalSection('api', 'API Reference', '/api-docs');

        $indexGroup = new DocumentationGroup('index', 'Documentation Home');
        $indexGroup->addSection('index', 'Documentation Home', 'docs/index.phtml');

        $docs = new Documentation;
        $docs->addGroup($indexGroup);
        $docs->addGroup($tutorialGroup);
        $docs->addGroup($referenceGroup);
        $docs->addGroup($apiGroup);

        return $docs;
    }),

    DocsAction::class => \DI\factory(function (ContainerInterface $c) {
        return new DocsAction($c->get(PhpRenderer::class), $c->get(Documentation::class));
    }),

    ApiDocsAction::class => \DI\factory(function (ContainerInterface $c) {
        return new ApiDocsAction($c->get(PhpRenderer::class), $c->get(DocGenerator::class), $c->get('cache'));
    }),

    TrackDownloads::class => function (ContainerInterface $c) {
        return new TrackDownloads($c->get(WorkshopRepository::class), $c->get(WorkshopInstallRepository::class));
    },

    SubmitWorkshop::class => \DI\factory(function (ContainerInterface $c) {
        return new SubmitWorkshop(
            $c->get(FormHandlerFactory::class)->create(
                new SubmitWorkshopInputFilter(new Client, $c->get(WorkshopRepository::class))
            ),
            new WorkshopCreator(new WorkshopComposerJsonInputFilter, $c->get(WorkshopRepository::class)),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

    //admin
    Login::class => \DI\factory(function (ContainerInterface $c) {
        return new Login(
            $c->get(AuthenticationService::class),
            $c->get(FormHandlerFactory::class)->create(new LoginInputFilter),
            $c->get(PhpRenderer::class)
        );
    }),

    ClearCacheAction::class => function (ContainerInterface $c) {
        return new ClearCacheAction(
            $c->get('cache.fpc'),
            $c->get(Messages::class)
        );
    },

    Requests::class => \DI\factory(function (ContainerInterface $c) {
        return new Requests(
            $c->get(WorkshopRepository::class),
            $c->get(PhpRenderer::class)
        );
    }),

    All::class => \DI\factory(function (ContainerInterface $c) {
        return new All(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(PhpRenderer::class)
        );
    }),

    Approve::class => \DI\factory(function (ContainerInterface $c) {
        return new Approve(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(Messages::class),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

    Promote::class => \DI\factory(function (ContainerInterface $c) {
        return new Promote(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(Messages::class)
        );
    }),

    Delete::class => \DI\factory(function (ContainerInterface $c) {
        return new Delete(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(Messages::class)
        );
    }),

    View::class => function (ContainerInterface $c) {
        return new View(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(PhpRenderer::class)
        );
    },

    'form.event' => function (ContainerInterface $c) {
        return $c->get(FormHandlerFactory::class)->create(new EventInputFilter);
    },

    EventAll::class => function (ContainerInterface $c) {
        return new EventAll($c->get(EventRepository::class), $c->get(PhpRenderer::class));
    },

    EventCreate::class => function (ContainerInterface $c) {
        return new EventCreate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
            $c->get(PhpRenderer::class),
            $c->get(Messages::class)
        );
    },

    EventUpdate::class => function (ContainerInterface $c) {
        return new EventUpdate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
            $c->get(PhpRenderer::class),
            $c->get(Messages::class)
        );
    },

    EventDelete::class => function (ContainerInterface $c) {
        return new EventDelete(
            $c->get(EventRepository::class),
            $c->get('cache.fpc'),
            $c->get(Messages::class)
        );
    },

    Messages::class => \DI\factory(function (ContainerInterface $c) {
        return new Messages();
    }),

    WorkshopFeed::class => \DI\factory(function (ContainerInterface $c) {
        return new WorkshopFeed(
            $c->get(WorkshopRepository::class),
            __DIR__ . '/../public/workshops.json'
        );
    }),

    WorkshopRepository::class => \DI\factory(function (ContainerInterface $c) {
        return $c->get(EntityManagerInterface::class)->getRepository(Workshop::class);
    }),

    WorkshopInstallRepository::class => \DI\factory(function (ContainerInterface $c) {
        return $c->get(EntityManagerInterface::class)->getRepository(WorkshopInstall::class);
    }),

    EventRepository::class => function (ContainerInterface $c) {
        return $c->get(EntityManagerInterface::class)->getRepository(Event::class);
    },

    AuthenticationService::class => \DI\factory(function (ContainerInterface $c) {
        $authService = new \Zend\Authentication\AuthenticationService;
        $authService->setAdapter(new Doctrine($c->get(EntityManagerInterface::class)));
        return new AuthenticationService($authService);
    }),

    Authenticator::class => \DI\factory(function(ContainerInterface $c) {
        return new Authenticator($c->get(AuthenticationService::class));
    }),

    Setup::class => \DI\factory(function (ContainerInterface $c) {
        $doctrineConfig = $c->get('config')['doctrine'];

        $config =  Setup::createAnnotationMetadataConfiguration(
            $doctrineConfig['meta']['entity_path'],
            $doctrineConfig['meta']['auto_generate_proxies'],
            $doctrineConfig['meta']['proxy_dir'],
            null,
            false
        );

        $config->setMetadataCacheImpl($c->get(DoctrineCache::class));
        $config->setQueryCacheImpl($c->get(DoctrineCache::class));
        $config->setHydrationCacheImpl($c->get(DoctrineCache::class));
        $config->setResultCacheImpl($c->get(DoctrineCache::class));

        return $config;
    }),

    EntityManagerInterface::class => \DI\factory(function (ContainerInterface $c) {
        Type::addType('uuid', UuidType::class);

        return EntityManager::create(
            $c->get('config')['doctrine']['connection'],
            $c->get(Setup::class)
        );
    }),

    ConsoleRunner::class => \DI\factory(function (ContainerInterface $c) {
        return ConsoleRunner::createHelperSet($c->get(EntityManagerInterface::class));
    }),

    EmailNotifier::class => function (ContainerInterface $c) {
        return new EmailNotifier(
            new \SendGrid(getenv('SEND_GRID_API_KEY')),
            getenv('SEND_GRID_SENDER_EMAIL')
        );
    },

    Generator::class => function (ContainerInterface $c) {
        return new Generator(
            new Parser,
            __DIR__ . '/../posts/',
            __DIR__ . '/../public/blog',
            $c->get(PhpRenderer::class)
        );
    },

    'config' => [
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => filter_var(getenv('DISPLAY_ERRORS'), FILTER_VALIDATE_BOOLEAN),
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

        'enablePageCache'   => filter_var(getenv('CACHE.FPC.ENABLE'), FILTER_VALIDATE_BOOLEAN),
        'enableCache'       => filter_var(getenv('CACHE.ENABLE'), FILTER_VALIDATE_BOOLEAN),
        'redisHost'         => getenv('REDIS_HOST'),

        'doctrine' => [
            'meta' => [
                'entity_path' => [
                    'src/Entity',
                    'src/User/Entity',
                ],
                'auto_generate_proxies' => true,
                'proxy_dir' =>  __DIR__.'/../cache/proxies',
            ],
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => getenv('MYSQL_HOST'),
                'dbname'   => getenv('MYSQL_DATABASE'),
                'user'     => getenv('MYSQL_USER'),
                'password' => getenv('MYSQL_PASSWORD'),
            ]
        ]
    ],

    //slim settings
    'settings.displayErrorDetails' => true,
 ];
