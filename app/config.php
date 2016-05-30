<?php

use function DI\factory;
use Interop\Container\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use PhpSchool\Website\Cache;
use PhpSchool\Website\Command\ClearCache;
use PhpSchool\Website\Command\GenerateDoc;
use PhpSchool\Website\DocGenerator;
use PhpSchool\Website\Documentation;
use PhpSchool\Website\DocumentationAction;
use PhpSchool\Website\DocumentationGroup;
use PhpSchool\Website\Middleware\FpcCache;
use Psr\Log\LoggerInterface;
use PhpSchool\Website\PhpRenderer;
use Stash\Driver\BlackHole;
use Stash\Driver\FileSystem;
use Stash\Pool;

$config = [
    'console' => factory(function (ContainerInterface $c) {
        $app = new Silly\Edition\PhpDi\Application('PHP School Website', null, $c);
        $app->command('generate-docs', $c->get(GenerateDoc::class));
        $app->command('clear-cache', $c->get(ClearCache::class));
        return $app;
    }),
    'app' => factory(function (ContainerInterface $c) {
        $app = new \Slim\App($c);
        $app->add($c->get(FpcCache::class));

        return $app;
    }),
    FpcCache::class => factory(function (ContainerInterface $c) {
        return new FpcCache($c->get('cache.fpc'));
    }),
    'cache.fpc' => factory(function (ContainerInterface $c) {

        if (!$c->get('config')['enablePageCache']) {
            return new Pool(new BlackHole);
        }

        return new Pool(new FileSystem(['path' => $c->get('config')['cacheDir']]));
    }),
    PhpRenderer::class => factory(function (ContainerInterface $c) {
        $settings = $c->get('config')['renderer'];

        $renderer = new PhpRenderer($settings['template_path'], [
            'links' => $c->get('config')['links'],
            'route' => $c->get('request')->getUri()->getPath(),
        ]);

        //default CSS
        $renderer->appendCss('/css/solarized-light.css');
        $renderer->appendCss('/css/core.css');
        $renderer->appendCss('https://fonts.googleapis.com/css?family=Open+Sans');


        //default JS
        $renderer->addJs('//code.jquery.com/jquery-1.12.0.min.js');
        $renderer->addJs('/js/highlight.min.js');
        $renderer->addJs('/js/main.js');

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

    //commands
    GenerateDoc::class => \DI\object(),
    ClearCache::class => factory(function (ContainerInterface $c) {
        return new ClearCache($c->get('cache.fpc'));
    }),

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
        $referenceGroup->addSection('exercise-checks', 'Exercise Checks', 'docs/reference/exercise-checks.phtml');
        $referenceGroup->addSection('bundled-checks', 'Bundled Checks', 'docs/reference/bundled-checks.phtml');
        $referenceGroup->addSection('creating-simple-checks', 'Creating Simple Checks', 'docs/reference/creating-simple-checks.phtml');
        $referenceGroup->addSection('events', 'Events', 'docs/reference/events.phtml');
        $referenceGroup->addSection('creating-listener-checks', 'Creating Listener Checks', 'docs/reference/creating-listener-checks.phtml');
        $referenceGroup->addSection('self-checking-exercises', 'Self Checking Exercises', 'docs/reference/self-checking-exercises.phtml');
        $referenceGroup->addSection('exercise-events', 'Exercise Events', 'docs/reference/exercise-events.phtml');
        $referenceGroup->addSection('patching-exercise-solutions', 'Patching Exercise Submissions', 'docs/reference/patching-exercise-solutions.phtml');
        $referenceGroup->addSection('creating-custom-results', 'Creating Custom Results', 'docs/reference/creating-custom-results.phtml', false);
        $referenceGroup->addSection('creating-custom-result-renderers', 'Creating Custom Result Renderers', 'docs/reference/creating-custom-result-renderers.phtml', false);

        $apiGroup = new DocumentationGroup('api', 'API');
        $apiGroup->addExternalSection('api', 'API Reference', '/docs/api', false);

        $indexGroup = new DocumentationGroup('index', 'Documentation Home');
        $indexGroup->addSection('index', 'Documentation Home', 'docs/index.phtml');

        $docs = new Documentation;
        $docs->addGroup($indexGroup);
        $docs->addGroup($tutorialGroup);
        $docs->addGroup($referenceGroup);
        $docs->addGroup($apiGroup);

        return $docs;
    }),

    DocumentationAction::class => \DI\factory(function (ContainerInterface $c) {
        return new DocumentationAction($c->get(Documentation::class), $c->get(PhpRenderer::class));
    }),

    'config' => [
        'displayErrorDetails' => true, // set to false in production
        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],
        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'links' => [
            'github'         => 'https://github.com/php-school/learn-you-php',
            'twitter'        => 'https://twitter.com/PHPSchoolTeam',
            'slack'          => 'https://phpschool.herokuapp.com',
            'discussions'    => 'https://github.com/php-school/discussions',
            'workshop'       => 'https://github.com/php-school/php-workshop',
            'github-website' => 'https://github.com/php-school/phpschool.io',
        ],

        'cacheDir'          => __DIR__ . '/../cache',
        'enablePageCache'   => true,
    ],

    //slim settings
    'settings.displayErrorDetails' => true,
 ];

if (file_exists(__DIR__ . '/dev-config.php')) {
    $config = array_replace_recursive($config, include __DIR__ . '/dev-config.php');
}

return $config;
