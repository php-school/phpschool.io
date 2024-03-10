<?php

namespace PhpSchool\Website\User\Middleware;

use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class StudentRefresher
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
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            return $handler->handle($request);
        }

        $entity = $this->studentRepository->findById($student->id);

        //student no longer exists
        if (null === $entity) {
            $this->session->delete('student');
            return $this->redirect('/');
        }

        //refresh user from database
        $this->session->set('student', $entity->toDTO());

        return $handler->handle($request);
    }
}
