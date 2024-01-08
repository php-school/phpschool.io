<?php

namespace PhpSchool\WebsiteTest\Cloud;

use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\Online\StudentWorkshopState;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use PhpSchool\Website\User\StudentRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class StudentWorkshopStateTest extends TestCase
{
    private SessionStorageInterface&MockObject $session;
    private StudentRepository&MockObject $studentRepository;

    public function setUp(): void
    {
        $this->session = $this->createMock(SessionStorageInterface::class);
        $this->studentRepository = $this->createMock(StudentRepository::class);
    }

    private function getStudent(array $state = []): StudentDTO
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
            new StudentCloudState($state)
        );
    }

    public function testCompleteExercise(): void
    {
        $student = $this->getStudent([
            'learnyouphp' => ['currentExercise' => 'Exercise 1', 'completedExercises' => []]
        ]);

        $entity = $this->getMockBuilder(Student::class)
            ->onlyMethods(['toDTO'])
            ->disableOriginalConstructor()
            ->getMock();

        $entity->expects($this->once())->method('toDTO')->willReturn($student);

        $this->studentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($student->id)
            ->willReturn($entity);

        $this->session
            ->expects($this->any())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        $this->studentRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function ($student) {
                $this->assertEquals(
                    [
                        'learnyouphp' => [
                            'completedExercises' => ['Exercise 1'],
                            'currentExercise' => 'Exercise 1'
                        ]
                    ],
                    $student->getWorkshopState()
                );

                return true;
            }));

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('student', $student);

        (new StudentWorkshopState($this->session, $this->studentRepository))
            ->completeExercise('learnyouphp', 'Exercise 1');
    }

    public function testSetCurrentExercise(): void
    {
        $student = $this->getStudent([
            'learnyouphp' => ['currentExercise' => 'Exercise 1', 'completedExercises' => ['Exercise 1']]
        ]);

        $entity = $this->getMockBuilder(Student::class)
            ->onlyMethods(['toDTO'])
            ->disableOriginalConstructor()
            ->getMock();

        $entity->expects($this->once())->method('toDTO')->willReturn($student);

        $this->studentRepository
            ->expects($this->once())
            ->method('findById')
            ->with($student->id)
            ->willReturn($entity);

        $this->session
            ->expects($this->any())
            ->method('get')
            ->with('student')
            ->willReturn($student);

        $this->studentRepository
            ->expects($this->once())
            ->method('update')
            ->with($this->callback(function ($student) {
                $this->assertEquals(
                    [
                        'learnyouphp' => [
                            'completedExercises' => ['Exercise 1'],
                            'currentExercise' => 'Exercise 2'
                        ]
                    ],
                    $student->getWorkshopState()
                );

                return true;
            }));

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('student', $student);

        (new StudentWorkshopState($this->session, $this->studentRepository))
            ->setCurrentExercise('learnyouphp', 'Exercise 2');
    }
}
