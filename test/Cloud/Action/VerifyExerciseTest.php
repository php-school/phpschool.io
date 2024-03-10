<?php

declare(strict_types=1);

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
use PhpSchool\Website\Action\Online\VerifyExercise;
use PhpSchool\Website\Online\CloudInstalledWorkshop;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\ProjectUploader;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\Online\VueResultsRenderer;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
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
        $session = $this->createMock(SessionStorageInterface::class);

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
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
            json_decode($actualResponse->getBody()->__toString(), true)
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
        $session = $this->createMock(SessionStorageInterface::class);

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
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
            json_decode($actualResponse->getBody()->__toString(), true)
        );
    }

    public function testErrorIsReturnedIfUploaderThrowsException(): void
    {
        [$installedWorkshopRepo,,,$session, $student] = $this->getDependencies();
        $state = $this->createMock(StudentWorkshopState::class);
        $renderer = $this->createMock(VueResultsRenderer::class);
        $uploader = $this->createMock(ProjectUploader::class);

        $request = new ServerRequest('POST', '/verify', [], json_encode([]));
        $uploader->expects($this->once())
            ->method('upload')
            ->with($request)
            ->willThrowException(new \RuntimeException('Some error'));

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'error' => 'Some error'
            ],
            json_decode($actualResponse->getBody()->__toString(), true)
        );
    }

    public function testWithDefaultEntryPoint(): void
    {
        $solution = $this->createProjectSolution();

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher, $session, $student] = $this->getDependencies();
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
        $uploader->expects($this->once())->method('upload')->with($request, $student)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->once())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => true,
                'results' => []
            ],
            json_decode($actualResponse->getBody()->__toString(), true)
        );
        $this->assertFileDoesNotExist($solution->getBaseDirectory());
    }

    public function testWithCustomEntryPoint(): void
    {
        $solution = $this->createProjectSolution('program.php');

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher, $session, $student] = $this->getDependencies();
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
        $uploader->expects($this->once())->method('upload')->with($request, $student)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->once())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => true,
                'results' => []
            ],
            json_decode($actualResponse->getBody()->__toString(), true)
        );
        $this->assertFileDoesNotExist($solution->getBaseDirectory());
    }

    public function testVerifyFail(): void
    {
        $solution = $this->createProjectSolution();

        [$installedWorkshopRepo, $exercise, $exerciseDispatcher, $session, $student] = $this->getDependencies();

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
        $uploader->expects($this->once())->method('upload')->with($request, $student)->willReturn($solution);
        $state = $this->createMock(StudentWorkshopState::class);
        $state->expects($this->never())->method('completeExercise')->with('workshop', 'Exercise');

        $controller = new VerifyExercise($installedWorkshopRepo, $uploader, $session, $state, $renderer);
        $response = new Response();

        $actualResponse = $controller->__invoke($request, $response, 'workshop', 'exercise');

        $this->assertEquals(200, $actualResponse->getStatusCode());
        $this->assertEquals('application/json', $actualResponse->getHeaderLine('Content-Type'));
        $this->assertEquals(
            [
                'success' => false,
                'results' => []
            ],
            json_decode($actualResponse->getBody()->__toString(), true)
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

        $student = $this->getStudent();
        $session = $this->createMock(SessionStorageInterface::class);
        $session->expects($this->any())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        return [$installedWorkshopRepo, $exercise, $dispatcher, $session, $student];
    }

    private function getStudent(): StudentDTO
    {
        return new StudentDTO(
            Uuid::uuid4(),
            'Student',
            'student@phpschool.io',
            'Student',
            null,
            null,
            new \DateTime(),
            false,
            new StudentCloudState([])
        );
    }
}
