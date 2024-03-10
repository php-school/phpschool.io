<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\User\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PhpSchool\Website\Online\StudentCloudState;
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

    public function testThatNonLoggedInStudentReceives401(): void
    {
        $request = new ServerRequest('GET', '/api/online/workshop/verify');

        $handler = $this->getRequestHandler();

        $middleware = new StudentAuthenticator(
            $this->session,
            $this->studentRepository
        );

        $response = $middleware->__invoke($request, $handler);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testThatLoggedInStudentCanAccessRoutes(): void
    {
        $request = new ServerRequest('GET', '/api/online/workshop/verify');

        $handler = $this->getRequestHandler();

        $student = $this->getStudent();

        $this->session->expects($this->once())
            ->method('get')
            ->with('student')
            ->willReturn($student);

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
            false,
            new StudentCloudState([])
        );
    }

    private function getRequestHandler(): RequestHandlerInterface
    {
        return new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response();
            }
        };
    }
}
