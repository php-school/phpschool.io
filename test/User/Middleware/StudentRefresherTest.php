<?php

namespace PhpSchool\WebsiteTest\User\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Middleware\StudentAuthenticator;
use PhpSchool\Website\User\Middleware\StudentRefresher;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class StudentRefresherTest extends TestCase
{
    private SessionStorageInterface&MockObject $session;
    private StudentRepository&MockObject $studentRepository;

    public function setUp(): void
    {
        $this->session = $this->createMock(SessionStorageInterface::class);
        $this->studentRepository = $this->createMock(StudentRepository::class);
    }

    public function testThatLoggedInStudentIsRefreshedWhenAccessingCloudRoutes(): void
    {
        $request = new ServerRequest('GET', '/cloud/verify');

        $handler = $this->getRequestHandler();

        $student = $this->getStudent();

        $entity = $this->createMock(Student::class);
        $entity->expects($this->once())->method('toDTO')->willReturn($student);

        $this->session->expects($this->once())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        $this->session->expects($this->once())
            ->method('set')
            ->with('student', $student);

        $this->studentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($student->id)
            ->willReturn($entity);

        $middleware = new StudentRefresher(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
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

    private function getRequestHandler(): RequestHandlerInterface
    {
        return new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
    }
}
