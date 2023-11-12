<?php

namespace PhpSchool\Website\User\Middleware;

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

        //if not a online route
        if (!str_starts_with($path, '/online')) {
            return $handler->handle($request);
        }

        $student = $this->session->get('student');

        //if on cloud home page allow guests
        if ($student === null && $path === '/online/dashboard') {
            return $handler->handle($request);
        }

        //if on any other cloud route, student must be logged in
        if ($student instanceof StudentDTO) {
            return $handler->handle($request);
        }

        return $this->redirectToDashboard();
    }
}
