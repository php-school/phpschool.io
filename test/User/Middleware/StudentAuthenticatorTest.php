<?php

namespace PhpSchool\WebsiteTest\User\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Cloud\StudentCloudState;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Middleware\StudentAuthenticator;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Uuid;

class StudentAuthenticatorTest extends TestCase
{
    private SessionStorageInterface&MockObject $session;
    private StudentRepository&MockObject $studentRepository;

    public function setUp(): void
    {
        $this->session = $this->createMock(SessionStorageInterface::class);
        $this->studentRepository = $this->createMock(StudentRepository::class);
    }

    public function testThatNonExistentRouteSkipsMiddleware(): void
    {
        $request = new ServerRequest('GET', '/non-cloud-route');

        $handler = $this->getRequestHandler();

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
    }

    public function testThatNonCloudRouteSkipsMiddleware(): void
    {
        $request = new ServerRequest('GET', '/');

        $handler = $this->getRequestHandler();

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
    }

    public function testThatNonLoggedInStudentCanAccessCloudHome(): void
    {
        $request = new ServerRequest('GET', '/cloud');

        $handler = $this->getRequestHandler();

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
    }

    public function testThatNonLoggedInStudentIsRedirectedToCloudHomeWhenAccessingAnyOtherCloudRoute(): void
    {
        $request = new ServerRequest('GET', '/cloud/verify');

        $handler = $this->getRequestHandler();

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/cloud', $response->getHeaderLine('Location'));
    }

    public function testThatLoggedInStudentCanAccessCloudHome(): void
    {
        $request = new ServerRequest('GET', '/cloud');

        $handler = $this->getRequestHandler();

        $student = $this->getStudent();

        $entity = $this->createMock(Student::class);
        $entity->expects($this->once())->method('toDTO')->willReturn($student);

        $this->session->expects($this->once())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        $this->studentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($student->id)
            ->willReturn($entity);

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
    }

    public function testThatLoggedInStudentCanAccessOtherCloudRoutes(): void
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

        $this->studentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($student->id)
            ->willReturn($entity);

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEmpty($response->getHeaderLine('Location'));
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

        $middleware = new StudentAuthenticator(
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
