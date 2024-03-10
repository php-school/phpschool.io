<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action\Online;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TourComplete
{
    use JsonUtils;

    public function __construct(
        private readonly SessionStorageInterface $session,
        private readonly StudentRepository $studentRepository
    ) {}

    public function __invoke(Request $request, Response $response): Response
    {
        $studentDTO = $this->getStudent();

        /** @var Student $student */
        $student = $this->studentRepository->findById($studentDTO->id);
        $student->setTourComplete();

        $this->studentRepository->update($student);

        return $this->jsonSuccess($response);
    }

    private function getStudent(): StudentDTO
    {
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            throw new \RuntimeException('Needs a logged in user');
        }

        return $student;
    }
}
