<?php

namespace PhpSchool\WebsiteTest\Cloud\Action;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\Website\Action\Online\WorkshopExercise;
use PhpSchool\Website\Online\CloudInstalledWorkshop;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProblemFileConverter;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\TestUtils\BaseFilesystemTest;
use PhpSchool\Website\User\ArraySession;
use PhpSchool\WebsiteTest\Asset\CliExerciseImpl;
use PhpSchool\WebsiteTest\Asset\ExerciseImpl;
use RuntimeException;

class ExerciseTest extends BaseFilesystemTest
{
    public function testErrorIsReturnedIfWorkshopDoesNotExist(): void
    {
        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willThrowException(new RuntimeException('Cannot find workshop'));

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new WorkshopExercise($installedWorkshopRepo, $problemFileConverter, new ArraySession());
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = $controller->__invoke($request, new Response(), $renderer, 'workshop', 'exercise');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'error' => 'Cannot find workshop'
        ], json_decode($response->getBody()->__toString(), true));
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
        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new WorkshopExercise($installedWorkshopRepo, $problemFileConverter, new ArraySession());
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));
        $response = $controller->__invoke($request, new Response(), $renderer, 'workshop', 'exercise');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals([
            'success' => false,
            'error' => 'Cannot find exercise'
        ], json_decode($response->getBody()->__toString(), true));
    }

    public function testWithBasicExercise(): void
    {
        [$installedWorkshopRepo, $workshop, $exercise] = $this->getDependencies();

        $problemFileConverter = $this->createMock(ProblemFileConverter::class);
        $problemFileConverter->expects($this->once())
            ->method('htmlFromExercise')
            ->with($exercise)
            ->willReturn('<h1>Some problem file</h1>');

        $renderer = new PhpRenderer('');

        $controller = new WorkshopExercise($installedWorkshopRepo, $problemFileConverter, new ArraySession());
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));

        $response = $controller->__invoke($request, new Response(), $renderer, 'workshop', 'exercise');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'student' => null,
            'workshop' => [],
            'exercise' => [
                'name' => 'my-exercise',
                'slug' => 'my-exercise',
                'description' => 'my-exercise',
                'type' => $exercise->getType()
            ],
            'problem' => '<h1>Some problem file</h1>',
            'totalExerciseCount' => 10,
            'initial_files' => [
                ['name' => 'solution.php', 'content' => '<?php ']
            ],
            'entry_point' => 'solution.php',
        ], json_decode($response->getBody()->__toString(), true));
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

        $renderer = $this->createMock(PhpRenderer::class);

        $controller = new WorkshopExercise($installedWorkshopRepo, $problemFileConverter, new ArraySession());
        $request = new ServerRequest('POST', '/editor', [], json_encode([]));

        $renderer->expects($this->once())
            ->method('slug')
            ->with('my-exercise')
            ->willReturn('my-exercise');

        $response = $controller->__invoke($request, new Response(), $renderer, 'workshop', 'exercise');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals([
            'student' => null,
            'workshop' => [],
            'exercise' => [
                'name' => 'my-exercise',
                'slug' => 'my-exercise',
                'description' => 'my-exercise',
                'type' => $exercise->getType()
            ],
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
            ],
        ], json_decode($response->getBody()->__toString(), true));
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
