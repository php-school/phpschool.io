<?php

use ahinkle\PackagistLatestVersion\PackagistLatestVersion;
use DI\Bridge\Slim\Bridge;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Element\IndentedCode;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Extension\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;
use League\OAuth2\Client\Provider\Github;
use PhpSchool\CliMdRenderer\CliExtension;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Markdown\CurrentContext;
use PhpSchool\PhpWorkshop\Markdown\ProblemFileExtension;
use PhpSchool\PhpWorkshop\Markdown\Renderer\ContextSpecificRenderer;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\AppName;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\Run;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Cloud\Verify;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Context;
use PhpSchool\PhpWorkshop\Markdown\Shorthands\Documentation as DocumentationShorthand;
use PhpSchool\Website\Action\StudentLogin;
use PhpSchool\Website\Cloud\Action\ComposerPackageAdd;
use PhpSchool\Website\Cloud\Action\ExerciseEditor;
use PhpSchool\Website\Cloud\Action\ListWorkshops;
use PhpSchool\Website\Cloud\Action\RunExercise;
use PhpSchool\Website\Cloud\Action\VerifyExercise;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\Command\DownloadComposerPackageList;
use PhpSchool\Website\Cloud\Middleware\ExerciseRunnerRateLimiter;
use PhpSchool\Website\Cloud\Middleware\Styles;
use PhpSchool\Website\Cloud\Middleware\ViteProductionAssets;
use PhpSchool\Website\Cloud\ProblemFileConverter;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\Middleware\FlashMessages as FlashMessagesMiddleware;
use PhpSchool\Website\Middleware\Session as SessionMiddleware;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Middleware\StudentAuthenticator;
use PhpSchool\Website\User\FlashMessages;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentRepository;
use PhpSchool\Website\ViteManifest;
use Predis\Connection\ConnectionException;
use Slim\App;
use Spatie\CommonMarkHighlighter\FencedCodeRenderer;
use Spatie\CommonMarkHighlighter\IndentedCodeRenderer;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;
use Symfony\Contracts\Cache\CacheInterface;
use function DI\factory;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Github\Client;
use Psr\Container\ContainerInterface;
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
use PhpSchool\Website\Action\TrackDownloads;
use PhpSchool\Website\Action\SubmitWorkshop;
use PhpSchool\Website\Blog\Generator;
use PhpSchool\Website\Command\ClearCache;
use PhpSchool\Website\Command\CreateAdminUser;
use PhpSchool\Website\Command\GenerateBlog;
use PhpSchool\Website\Documentation;
use PhpSchool\Website\DocumentationGroup;
use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Form\FormHandlerFactory;
use PhpSchool\Website\InputFilter\SubmitWorkshop as SubmitWorkshopInputFilter;
use PhpSchool\Website\Middleware\FpcCache;
use PhpSchool\Website\Repository\EventRepository;
use PhpSchool\Website\Repository\WorkshopInstallRepository;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Service\WorkshopCreator;
use PhpSchool\Website\User\Adapter\Doctrine;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\User\Middleware\AdminAuthenticator;
use PhpSchool\Website\InputFilter\Event as EventInputFilter;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;
use PhpSchool\Website\InputFilter\Login as LoginInputFilter;
use PhpSchool\Website\Workshop\EmailNotifier;
use PhpSchool\Website\WorkshopFeed;
use Psr\Log\LoggerInterface;
use PhpSchool\Website\PhpRenderer;
use Ramsey\Uuid\Doctrine\UuidType;
use PhpSchool\Website\User\Session;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use function DI\get;

return [
    'console' => factory(function (DI\Container $c): Silly\Edition\PhpDi\Application {
        $app = new Silly\Edition\PhpDi\Application('PHP School Website', 'UNKNOWN', $c);
        $app->command('clear-cache', ClearCache::class);
        $app->command('create-admin-user name email password', CreateAdminUser::class);
        $app->command('generate-blog', GenerateBlog::class);
        $app->command('download-composer-packages', DownloadComposerPackageList::class);

        ConsoleRunner::addCommands($app, new SingleManagerProvider($c->get(EntityManagerInterface::class)));

        return $app;
    }),
    'app' => factory(function (ContainerInterface $c): App {
        $app =  Bridge::create($c);
        $app->addRoutingMiddleware();
        $app->add($c->get(FpcCache::class));
        $app->add(FlashMessagesMiddleware::class);
        $app->add(new SessionMiddleware(['name' => 'phpschool']));

        return $app;
    }),
    FlashMessagesMiddleware::class => function (ContainerInterface $c): FlashMessagesMiddleware {
        return new FlashMessagesMiddleware($c->get(FlashMessages::class), $c->get(PhpRenderer::class));
    },
    FpcCache::class => factory(function (ContainerInterface $c): FpcCache {
        return new FpcCache($c->get('cache.fpc'));
    }),
    'cache.fpc' => factory(function (ContainerInterface $c): CacheInterface {
        if (!$c->get('config')['enablePageCache']) {
            return new NullAdapter;
        }
        return new RedisAdapter(new Predis\Client(['host' => $c->get('config')['redisHost']]), 'fpc');
    }),
    'cache' => factory(function (ContainerInterface $c): CacheInterface {
        if (!$c->get('config')['enableCache']) {
            return new NullAdapter;
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
    ViteProductionAssets::class => function (ContainerInterface $c): ViteProductionAssets {
        return new ViteProductionAssets(
            $c->get(PhpRenderer::class),
            $c->get(ViteManifest::class)
        );
    },
    ViteManifest::class => function (ContainerInterface $c): ViteManifest {
        return new ViteManifest();
    },
    PhpRenderer::class => factory(function (ContainerInterface $c): PhpRenderer {
        $settings = $c->get('config')['renderer'];
        $renderer = new PhpRenderer(
            $settings['template_path'],
            [
                'links'  => $c->get('config')['links'],
            ]
        );

        $renderer->addJs('jquery', '//code.jquery.com/jquery-1.12.0.min.js');
        $renderer->addJs('highlight.js', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js');

        $manifest = $c->get(ViteManifest::class);
        $renderer->addJs('main-js', $manifest->assetUrl('main.js'));

        foreach ($manifest->cssUrls('main.js') as $i => $url) {
            $renderer->appendRemoteCss($i, $url);
        }

        $renderer->appendRemoteCss('font', 'https://fonts.googleapis.com/css?family=Open+Sans: 400,700');

        return $renderer;
    }),
    LoggerInterface::class => factory(function (ContainerInterface $c): LoggerInterface{
        $settings = $c->get('config')['logger'];
        $logger = new Logger($settings['name']);
        $logger->pushProcessor(new UidProcessor);
        $logger->pushHandler(new StreamHandler($settings['path'], Logger::DEBUG));
        return $logger;
    }),

    SessionStorageInterface::class => get(Session::class),

    Session::class => function (ContainerInterface $c): Session {
        return new Session;
    },

    FormHandlerFactory::class => function (ContainerInterface $c): FormHandlerFactory {
        return new FormHandlerFactory($c->get(Session::class));
    },

    //commands
    ClearCache::class => factory(function (ContainerInterface $c): ClearCache {
        return new ClearCache($c->get('cache.fpc'));
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

    Documentation::class => \DI\factory(function (ContainerInterface $c): Documentation {
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


        $indexGroup = new DocumentationGroup('index', 'Documentation Home');
        $indexGroup->addSection('index', 'Documentation Home', 'docs/index.phtml');

        $docs = new Documentation;
        $docs->addGroup($indexGroup);
        $docs->addGroup($tutorialGroup);
        $docs->addGroup($referenceGroup);

        return $docs;
    }),

    DocsAction::class => \DI\factory(function (ContainerInterface $c): DocsAction {
        return new DocsAction($c->get(PhpRenderer::class), $c->get(Documentation::class));
    }),

    TrackDownloads::class => function (ContainerInterface $c): TrackDownloads {
        return new TrackDownloads($c->get(WorkshopRepository::class), $c->get(WorkshopInstallRepository::class));
    },

    SubmitWorkshop::class => \DI\factory(function (ContainerInterface $c): SubmitWorkshop {
        return new SubmitWorkshop(
            $c->get(FormHandlerFactory::class)->create(
                new SubmitWorkshopInputFilter(new Client, $c->get(WorkshopRepository::class))
            ),
            new WorkshopCreator(new WorkshopComposerJsonInputFilter, $c->get(WorkshopRepository::class)),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

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
            $c->get(FormHandlerFactory::class)->create(new LoginInputFilter),
            $c->get(PhpRenderer::class),
            $c->get(ViteManifest::class)
        );
    }),

    ClearCacheAction::class => function (ContainerInterface $c): ClearCacheAction {
        return new ClearCacheAction(
            $c->get('cache.fpc'),
            $c->get(FlashMessages::class)
        );
    },

    Requests::class => \DI\factory(function (ContainerInterface $c): Requests {
        return new Requests(
            $c->get(WorkshopRepository::class),
            $c->get(PhpRenderer::class)
        );
    }),

    All::class => \DI\factory(function (ContainerInterface $c): All {
        return new All(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(PhpRenderer::class)
        );
    }),

    Approve::class => \DI\factory(function (ContainerInterface $c): Approve {
        return new Approve(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(FlashMessages::class),
            $c->get(EmailNotifier::class),
            $c->get(LoggerInterface::class)
        );
    }),

    Promote::class => \DI\factory(function (ContainerInterface $c): Promote {
        return new Promote(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(FlashMessages::class)
        );
    }),

    Delete::class => \DI\factory(function (ContainerInterface $c): Delete {
        return new Delete(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(WorkshopFeed::class),
            $c->get('cache.fpc'),
            $c->get(FlashMessages::class)
        );
    }),

    View::class => function (ContainerInterface $c): View {
        return new View(
            $c->get(WorkshopRepository::class),
            $c->get(WorkshopInstallRepository::class),
            $c->get(PhpRenderer::class)
        );
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
        $environment->addBlockRenderer(FencedCode::class, new FencedCodeRenderer(['html', 'php', 'js', 'bash']));
        $environment->addBlockRenderer(IndentedCode::class, new IndentedCodeRenderer(['html', 'php', 'js', 'bash']));

        $environment
            ->addExtension(new ProblemFileExtension(
                $c->get(ContextSpecificRenderer::class),
                [
                    'appname' => new AppName(),
                    'doc' => new DocumentationShorthand(),
                    'run' => new Run(),
                    'verify' => new Verify(),
                    'context' => $c->get(Context::class)
                ]
            ));

        return new MarkdownConverter($environment);
    },

    ProblemFileConverter::class => function (ContainerInterface $c): ProblemFileConverter {
        return new ProblemFileConverter($c->get(MarkdownConverterInterface::class));
    },

    ListWorkshops::class => function (ContainerInterface $c): ListWorkshops {
        return new ListWorkshops(
            $c->get(CloudWorkshopRepository::class),
        );
    },

    ExerciseEditor::class => function (ContainerInterface $c): ExerciseEditor {
        return new ExerciseEditor(
            $c->get(CloudWorkshopRepository::class),
            $c->get(ProblemFileConverter::class),
            $c->get(StudentWorkshopState::class)
        );
    },

    RunExercise::class => function (ContainerInterface $c): RunExercise {
        return new RunExercise(
            $c->get(CloudWorkshopRepository::class),
            $c->get(ExerciseDispatcher::class),
        );
    },

    VerifyExercise::class => function (ContainerInterface $c): VerifyExercise {
        return new VerifyExercise(
            $c->get(CloudWorkshopRepository::class),
            $c->get(ExerciseDispatcher::class),
            $c->get(StudentWorkshopState::class)
        );
    },

    'form.event' => function (ContainerInterface $c): FormHandler {
        return $c->get(FormHandlerFactory::class)->create(new EventInputFilter);
    },

    EventAll::class => function (ContainerInterface $c): EventAll {
        return new EventAll($c->get(EventRepository::class), $c->get(PhpRenderer::class));
    },

    EventCreate::class => function (ContainerInterface $c): EventCreate {
        return new EventCreate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
            $c->get(PhpRenderer::class),
            $c->get(FlashMessages::class)
        );
    },

    EventUpdate::class => function (ContainerInterface $c): EventUpdate {
        return new EventUpdate(
            $c->get(EventRepository::class),
            $c->get('form.event'),
            $c->get(PhpRenderer::class),
            $c->get(FlashMessages::class)
        );
    },

    EventDelete::class => function (ContainerInterface $c): EventDelete {
        return new EventDelete(
            $c->get(EventRepository::class),
            $c->get('cache.fpc'),
            $c->get(FlashMessages::class)
        );
    },

    FlashMessages::class => \DI\factory(function (ContainerInterface $c): FlashMessages {
        return new FlashMessages($c->get(Session::class));
    }),

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

    StudentRepository::class => function (ContainerInterface $c): StudentRepository {
        return $c->get(EntityManagerInterface::class)->getRepository(Student::class);
    },

    AdminAuthenticationService::class => \DI\factory(function (ContainerInterface $c): AdminAuthenticationService {
        $authService = new \Laminas\Authentication\AuthenticationService;
        $authService->setAdapter(new Doctrine($c->get(EntityManagerInterface::class)));
        return new AdminAuthenticationService($authService);
    }),

    AdminAuthenticator::class => \DI\factory(function (ContainerInterface $c): AdminAuthenticator {
        return new AdminAuthenticator($c->get(AdminAuthenticationService::class));
    }),

    StudentAuthenticator::class => function (ContainerInterface $c): StudentAuthenticator {
        return new StudentAuthenticator(
            $c->get(Session::class),
            $c->get(StudentRepository::class)
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

        return EntityManager::create(
            $c->get('config')['doctrine']['connection'],
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
            new Parser,
            __DIR__ . '/../posts/',
            __DIR__ . '/../public/blog',
            $c->get(PhpRenderer::class)
        );
    },

    Styles::class => function (ContainerInterface $c) {
        return new Styles($c->get(PhpRenderer::class));
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

        'enablePageCache'   => filter_var($_ENV['CACHE.FPC.ENABLE'], FILTER_VALIDATE_BOOLEAN),
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
                'proxy_dir' =>  __DIR__.'/../cache/proxies',
            ],
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => $_ENV['MYSQL_HOST'],
                'dbname'   => $_ENV['MYSQL_DATABASE'],
                'user'     => $_ENV['MYSQL_USER'],
                'password' => $_ENV['MYSQL_PASSWORD'],
            ]
        ],

        'github' => [
            'clientId' => $_ENV['GITHUB_CLIENT_ID'],
            'clientSecret' => $_ENV['GITHUB_CLIENT_SECRET'],
        ]
    ],

    //slim settings
    'settings.displayErrorDetails' => filter_var($_ENV['DISPLAY_ERRORS'], FILTER_VALIDATE_BOOLEAN),
 ];
