<?php

namespace PhpSchool\WebsiteTest\Cloud\Action;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\PhpWorkshop\Exercise\ExerciseInterface;
use PhpSchool\PhpWorkshop\ExerciseDispatcher;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Result\Failure;
use PhpSchool\PhpWorkshop\ResultAggregator;
use PhpSchool\PhpWorkshop\Solution\DirectorySolution;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\Cloud\Action\VerifyExercise;
use PhpSchool\Website\Cloud\CloudInstalledWorkshop;
use PhpSchool\Website\Cloud\CloudWorkshopRepository;
use PhpSchool\Website\Cloud\ProjectUploader;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use PhpSchool\Website\Cloud\VueResultsRenderer;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class VerifyExerciseTest extends TestCase
{
    public function testErrorIsReturnedIfWorkshopDoesNotExist(): void
    {
        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willThrowException(new RuntimeException('Cannot find workshop'));

        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $request = new ServerRequest('POST', '/verify', [], json_encode([]));
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'error' => 'Cannot find workshop'
            ],
            json_decode($actualResponse->getBody(), true)
        );
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

        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $request = new ServerRequest('POST', '/verify', [], json_encode([]));
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'error' => 'Cannot find exercise'
            ],
            json_decode($actualResponse->getBody(), true)
        );
    }

    public function testErrorIsReturnedIfUploaderThrowsException(): void
    {
        [$installedWorkshopRepo] = $this->getDependencies();
        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);

        $request = new ServerRequest('POST', '/verify', [], json_encode([]));
        $uploader->expects($this->once())
            ->method('upload')
            ->with($request)
            ->willThrowException(new \RuntimeException('Some error'));

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'error' => 'Some error'
            ],
            json_decode($actualResponse->getBody(), true)
        );
    }

    public function testWithDefaultEntryPoint(): void
    {
        $solution = $this->createProjectSolution();

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher] = $this->getDependencies();
        $exerciseDispatcher->expects($this->once())
            ->method('verify')
            ->with($exercise, $this->callback(function (Input $input) use ($solution) {
                $this->assertEquals('workshop', $input->getAppName());
                $this->assertEquals($solution->getEntryPoint()->getAbsolutePath(), $input->getArgument('program'));

                return true;
            }))
            ->willReturn(new ResultAggregator());

        $request = new ServerRequest('POST', '/verify', [], json_encode([]));

        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);
        $uploader->expects($this->once())->method('upload')->with($request)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->once())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => true,
                'results' => []
            ],
            json_decode($actualResponse->getBody(), true)
        );
        $this->assertFileDoesNotExist($solution->getBaseDirectory());
    }

    public function testWithCustomEntryPoint(): void
    {
        $solution = $this->createProjectSolution('program.php');

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher] = $this->getDependencies();
        $exerciseDispatcher->expects($this->once())
            ->method('verify')
            ->with($exercise, $this->callback(function (Input $input) use ($solution) {
                $this->assertEquals('workshop', $input->getAppName());
                $this->assertEquals($solution->getEntryPoint()->getAbsolutePath(), $input->getArgument('program'));

                return true;
            }))
            ->willReturn(new ResultAggregator());

        $request = new ServerRequest('POST', '/verify', [], json_encode([]));

        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);
        $uploader->expects($this->once())->method('upload')->with($request)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->once())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => true,
                'results' => []
            ],
            json_decode($actualResponse->getBody(), true)
        );
        $this->assertFileDoesNotExist($solution->getBaseDirectory());
    }

    public function testVerifyFail(): void
    {
        $solution = $this->createProjectSolution();

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher] = $this->getDependencies();

        $results = new ResultAggregator();
        $results->add(new Failure('Failure', 'Failure'));
        $exerciseDispatcher->expects($this->once())
            ->method('verify')
            ->with($exercise, $this->callback(function (Input $input) use ($solution) {
                $this->assertEquals('workshop', $input->getAppName());
                $this->assertEquals($solution->getEntryPoint()->getAbsolutePath(), $input->getArgument('program'));

                return true;
            }))
            ->willReturn($results);

        $request = new ServerRequest('POST', '/verify', [], json_encode([]));

        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);
        $uploader->expects($this->once())->method('upload')->with($request)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->never())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'results' => []
            ],
            json_decode($actualResponse->getBody(), true)
        );
        $this->assertFileDoesNotExist($solution->getBaseDirectory());
    }

    private function createProjectSolution(string $entryPoint = 'solution.php'): DirectorySolution
    {
        $base = System::tempDir($this->getName());
        mkdir($base, 0777, true);
        file_put_contents($base . '/' . $entryPoint, '<?php echo "Hello World";');
        return new DirectorySolution($base, $entryPoint, []);
    }

    /**
     * @return array{0: CloudWorkshopRepository, 1: ExerciseInterface, 2: ExerciseDispatcher}
     */
    private function getDependencies(): array
    {
        $exercise = $this->createMock(ExerciseInterface::class);
        $exercise->expects($this->any())->method('getName')->willReturn('Exercise');

        $workshop = $this->createMock(CloudInstalledWorkshop::class);
        $workshop->expects($this->once())
            ->method('findExerciseBySlug')
            ->with('exercise')
            ->willReturn($exercise);

        $workshop->expects($this->any())
            ->method('getCode')
            ->willReturn('workshop');

        $dispatcher = $this->createMock(ExerciseDispatcher::class);
        $workshop->expects($this->any())
            ->method('getExerciseDispatcher')
            ->willReturn($dispatcher);

        $installedWorkshopRepo = $this->createMock(CloudWorkshopRepository::class);
        $installedWorkshopRepo->expects($this->once())
            ->method('findByCode')
            ->with('workshop')
            ->willReturn($workshop);

        return [$installedWorkshopRepo, $exercise, $dispatcher];
    }
}
