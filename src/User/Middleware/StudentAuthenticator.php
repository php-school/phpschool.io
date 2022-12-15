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

        //if not a cloud route
        if (!str_starts_with($path, '/cloud')) {
            return $handler->handle($request);
        }

        $student = $this->session->get('student');

        //if on cloud home page allow guests
        if ($student === null && $path === '/cloud') {
            return $handler->handle($request);
        }

        //if on any other cloud route, student must be logged in
        if ($student instanceof StudentDTO) {
            $entity = $this->studentRepository->findById($student->id);

            //student no longer exists
            if (null === $entity) {
                $this->session->delete('student');
                return $this->redirect('/cloud');
            }

            //refresh user from database
            $this->session->set('student', $entity->toDTO());
            return $handler->handle($request);
        }

        return $this->redirect('/cloud');
    }
}
