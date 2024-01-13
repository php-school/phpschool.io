<?php

namespace PhpSchool\Website\User\Middleware;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class StudentAuthenticator
{
    use RedirectUtils;

    private SessionStorageInterface $session;
    private StudentRepository $studentRepository;

    public function __construct(SessionStorageInterface $session, StudentRepository $studentRepository)
    {
        $this->session = $session;
        $this->studentRepository = $studentRepository;
    }

    public function __invoke(Request $request, RequestHandler $handler): MessageInterface
    {
        $path = $request->getUri()->getPath();

        //if not an online route
        if (!str_starts_with($path, '/api/online')) {
            return $handler->handle($request);
        }

        $student = $this->session->get('student');

        //if on any other cloud route, student must be logged in
        if (!$student instanceof StudentDTO) {
            return new Response(401);
        }

        return $handler->handle($request);
    }
}
