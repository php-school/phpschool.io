<?php

namespace PhpSchool\Website\Cloud;

use PhpSchool\PhpWorkshop\UserState\UserState;
use PhpSchool\Website\User\Session;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;

class StudentWorkshopState
{
    private SessionStorageInterface $session;
    private StudentRepository $studentRepository;

    public function __construct(
        SessionStorageInterface $session,
        StudentRepository $studentRepository,
    ) {
        $this->session = $session;
        $this->studentRepository = $studentRepository;
    }

    private function getStudent(): StudentDTO
    {
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            throw new \RuntimeException('Needs a logged in user');
        }

        return $student;
    }

    public function setCurrentExercise(string $workshopCode, string $exerciseName): void
    {
        $state = $this->getState($workshopCode);
        $state->setCurrentExercise($exerciseName);
        $this->updateState($workshopCode, $state);
    }

    public function completeExercise(string $workshopCode, string $exerciseName): void
    {
        $state = $this->getState($workshopCode);
        $state->addCompletedExercise($exerciseName);
        $this->updateState($workshopCode, $state);
    }

    private function updateState(string $workshopCode, UserState $state): void
    {
        $studentDTO = $this->getStudent();

        $student = $this->studentRepository->findById($studentDTO->id);
        $student->updateWorkshopState($workshopCode, $state);

        $this->studentRepository->update($student);

        //refresh student DTO
        $this->session->set('student', $student->toDTO());
    }

    public function getState(string $workshopCode): UserState
    {
        $studentDTO = $this->getStudent();

        return $studentDTO->workshopState->getStateForWorkshop($workshopCode);
    }
}
