<?php

namespace PhpSchool\WebsiteTest\Cloud\Action;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\Website\Cloud\Action\ExerciseEditor;
use PhpSchool\Website\Cloud\CloudInstalledWorkshop;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProblemFileConverter;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\TestUtils\BaseFilesystemTest;
use PhpSchool\WebsiteTest\Asset\CliExerciseImpl;
use PhpSchool\WebsiteTest\Asset\ExerciseImpl;
use RuntimeException;

class ExerciseEditorTest extends BaseFilesystemTest
{
    public function testRedirectIsReturnedIfWorkshopDoesNotExist(): void
    {
        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willThrowException(new RuntimeException('Cannot find workshop'));

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new ExerciseEditor($installedWorkshopRepo, $problemFileConverter, $state);
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, $renderer, 'workshop', 'exercise');

        $this->assertEquals(302, $actualResponse->getStatusCode());
        $this->assertEquals('/cloud', $actualResponse->getHeaderLine('Location'));
    }

    public function testErrorIsReturnedIfExerciseDoesNotExist(): void
    {
        $workshop = $this->createMock(CloudInstalledWorkshop::class);
        $workshop->expects($this->once())
            ->method('findExerciseBySlug')
            ->with('exercise')
            ->willThrowException(new RuntimeException('Cannot find exercise'));

        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willReturn($workshop);

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new ExerciseEditor($installedWorkshopRepo, $problemFileConverter, $state);
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, $renderer, 'workshop', 'exercise');

        $this->assertEquals(302, $actualResponse->getStatusCode());
        $this->assertEquals('/cloud', $actualResponse->getHeaderLine('Location'));
    }

    public function testWithBasicExercise(): void
    {
        [$installedWorkshopRepo, $workshop, $exercise] = $this->getDependencies();

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $problemFileConverter->expects($this->once())
            ->method('htmlFromExercise')
            ->with($exercise)
            ->willReturn('<h1>Some problem file</h1>');

        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new ExerciseEditor($installedWorkshopRepo, $problemFileConverter, $state);
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = new Response();

        $renderer->expects($this->once())
            ->method('slug')
            ->with('my-exercise')
            ->willReturn('my-exercise');

        $renderer->expects($this->once())
            ->method('fetch')
            ->with(
                'cloud/exercise-editor.phtml',
                [
                    'workshop' => $workshop,
                    'exercise' => [
                        'name' => 'my-exercise',
                        'slug' => 'my-exercise',
                        'description' => 'my-exercise',
                        'type' => $exercise->getType()
                    ],
                    'nextExerciseLink' => null,
                    'problem' => '<h1>Some problem file</h1>',
                    'totalExerciseCount' => 10,
                    'initial_files' => [
                        ['name' => 'solution.php', 'content' => '<?php ']
                    ],
                    'entry_point' => 'solution.php'
                ]
            )
            ->willReturn('innerContent');

        $renderer->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'layouts/cloud-editor.phtml',
                [
                    'pageTitle' => 'PHP School Cloud',
                    'pageDescription' => 'PHP School Cloud',
                    'content' => 'innerContent'
                ]
            )
            ->willReturn($response);

        $actualResponse = $controller->__invoke($request, $response, $renderer, 'workshop', 'exercise');

        $this->assertEquals($response, $actualResponse);
    }

    public function testWithNextExerciseLink(): void
    {
        [$installedWorkshopRepo, $workshop, $exercise] = $this->getDependencies();

        $nextExercise = new ExerciseImpl('Next Exercise');
        $workshop->expects($this->once())
            ->method('findNextExercise')
            ->with($exercise)
            ->willReturn($nextExercise);

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $problemFileConverter->expects($this->once())
            ->method('htmlFromExercise')
            ->with($exercise)
            ->willReturn('<h1>Some problem file</h1>');

        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new ExerciseEditor($installedWorkshopRepo, $problemFileConverter, $state);
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = new Response();

        $renderer->expects($this->exactly(2))
            ->method('slug')
            ->willReturnCallback(function ($arg0) {
                return match ($arg0) {
                    'Next Exercise' => 'next-exercise',
                    'my-exercise' => 'my-exercise',
                };
            });

        $renderer->expects($this->once())
            ->method('fetch')
            ->with(
                'cloud/exercise-editor.phtml',
                [
                    'workshop' => $workshop,
                    'exercise' => [
                        'name' => 'my-exercise',
                        'slug' => 'my-exercise',
                        'description' => 'my-exercise',
                        'type' => $exercise->getType()
                    ],
                    'nextExerciseLink' => '/cloud/workshop/workshop/exercise/next-exercise/editor',
                    'problem' => '<h1>Some problem file</h1>',
                    'totalExerciseCount' => 10,
                    'initial_files' => [
                        ['name' => 'solution.php', 'content' => '<?php ']
                    ],
                    'entry_point' => 'solution.php'
                ]
            )
            ->willReturn('innerContent');

        $renderer->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'layouts/cloud-editor.phtml',
                [
                    'pageTitle' => 'PHP School Cloud',
                    'pageDescription' => 'PHP School Cloud',
                    'content' => 'innerContent'
                ]
            )
            ->willReturn($response);

        $actualResponse = $controller->__invoke($request, $response, $renderer, 'workshop', 'exercise');

        $this->assertEquals($response, $actualResponse);
    }

    public function testWithOfficialSolution(): void
    {
        $exercise = new CliExerciseImpl();

        $this->getTemporaryFile('solution.php', 'ENTRYPOINT');
        $this->getTemporaryFile('some-class.php', 'SOME CLASS');

        $solution = DirectorySolution::fromDirectory($this->getTemporaryDirectory());
        $exercise->setSolution($solution);

        [$installedWorkshopRepo, $workshop] = $this->getDependencies($exercise);

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $problemFileConverter->expects($this->once())
            ->method('htmlFromExercise')
            ->with($exercise)
            ->willReturn('<h1>Some problem file</h1>');

        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new ExerciseEditor($installedWorkshopRepo, $problemFileConverter, $state);
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = new Response();

        $renderer->expects($this->once())
            ->method('slug')
            ->with('my-exercise')
            ->willReturn('my-exercise');

        $renderer->expects($this->once())
            ->method('fetch')
            ->with(
                'cloud/exercise-editor.phtml',
                [
                    'workshop' => $workshop,
                    'exercise' => [
                        'name' => 'my-exercise',
                        'slug' => 'my-exercise',
                        'description' => 'my-exercise',
                        'type' => $exercise->getType()
                    ],
                    'nextExerciseLink' => null,
                    'problem' => '<h1>Some problem file</h1>',
                    'totalExerciseCount' => 10,
                    'initial_files' => [
                        ['name' => 'solution.php', 'content' => '<?php ']
                    ],
                    'entry_point' => 'solution.php',
                    'official_solution' => [
                        [
                            'file_path' => 'solution.php',
                            'entry_point' => true,
                            'content' => 'RU5UUllQT0lOVA==',
                        ],
                        [
                            'file_path' => 'some-class.php',
                            'entry_point' => false,
                            'content' => 'U09NRSBDTEFTUw==',
                        ]
                    ]
                ]
            )
            ->willReturn('innerContent');

        $renderer->expects($this->once())
            ->method('render')
            ->with(
                $response,
                'layouts/cloud-editor.phtml',
                [
                    'pageTitle' => 'PHP School Cloud',
                    'pageDescription' => 'PHP School Cloud',
                    'content' => 'innerContent'
                ]
            )
            ->willReturn($response);

        $actualResponse = $controller->__invoke($request, $response, $renderer, 'workshop', 'exercise');

        $this->assertEquals($response, $actualResponse);
    }

    /**
     * @return array{0: CloudWorkshopRepository, 1: ExerciseInterface, 2: ExerciseDispatcher}
     */
    private function getDependencies(ExerciseInterface $exercise = null): array
    {
        $exercise = $exercise ?? new ExerciseImpl();

        $workshop = $this->createMock(CloudInstalledWorkshop::class);
        $workshop->expects($this->once())
            ->method('findExerciseBySlug')
            ->with('exercise')
            ->willReturn($exercise);

        $workshop->expects($this->any())
            ->method('getCode')
            ->willReturn('workshop');

        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willReturn($workshop);

        $installedWorkshopRepo->expects($this->once())
            ->method('totalExerciseCount')
            ->willReturn(10);

        return [$installedWorkshopRepo, $workshop, $exercise];
    }
}
