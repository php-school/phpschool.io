<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\User\Entity;

use PhpSchool\PhpWorkshop\UserState\UserState;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\Entity\Student;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class StudentTest extends TestCase
{
    public function testToDTO(): void
    {
        $state = [
            'learnyouphp' => [
                'currentExercise' => 'Exceptional Coding',
                "completedExercises" => [ "Hello World", "Baby Steps", "My First IO",]
            ]
        ];

        $args = ['GH1', 'Student 1', 'student@phpschool.com', 'Student 1', null, null, $state];

        $student = new class (...$args) extends Student {
            public function getId(): UuidInterface
            {
                return Uuid::uuid4();
            }
        };

        $dto = $student->toDTO();

        $this->assertEquals('Student 1', $dto->username);
        $this->assertEquals('student@phpschool.com', $dto->email);
        $this->assertEquals('Student 1', $dto->name);
        $this->assertNull($dto->profilePicture);
        $this->assertNull($dto->location);
        $this->assertFalse($dto->tourComplete);
        $this->assertInstanceOf(\DateTime::class, $dto->joinDate);

        $state = $dto->workshopState;

        $this->assertInstanceOf(StudentCloudState::class, $state);

        $this->assertEquals(3, $state->getTotalCompletedExercises());

        $workshopState = $state->getStateForWorkshop('learnyouphp');
        $this->assertEquals('Exceptional Coding', $workshopState->getCurrentExercise());
        $this->assertEquals([ "Hello World", "Baby Steps", "My First IO",], $workshopState->getCompletedExercises());
    }

    public function testGetSetWorkshopState(): void
    {
        $state = [
            'learnyouphp' => [
                'currentExercise' => 'Exceptional Coding',
                "completedExercises" => [ "Hello World", "Baby Steps", "My First IO",]
            ]
        ];

        $student = new Student('GH1', 'Student 1', 'student@phpschool.com', 'Student 1', null, null, $state);

        $this->assertEquals($state, $student->getWorkshopState());

        $student->setWorkshopState([]);

        $this->assertEquals([], $student->getWorkshopState());
    }

    public function testUpdateWorkshopState(): void
    {
        $state = [
            'learnyouphp' => [
                'currentExercise' => 'E3',
                "completedExercises" => [ "E1", "E2", "E3"]
            ]
        ];

        $student = new Student('GH1', 'Student 1', 'student@phpschool.com', 'Student 1', null, null, $state);

        $state = new UserState([ "E1", "E2", "E3", "E4"], 'E4');
        $student->updateWorkshopState('learnyouphp', $state);

        $this->assertEquals(
            [
                'learnyouphp' => [
                    'currentExercise' => 'E4',
                    "completedExercises" => [ "E1", "E2", "E3", "E4"]
                ]
            ],
            $student->getWorkshopState()
        );
    }

    public function testSetTourComplete(): void
    {
        $student = new Student('GH1', 'Student 1', 'student@phpschool.com', 'Student 1', null, null, []);

        $this->assertFalse($student->isTourComplete());

        $student->setTourComplete();

        $this->assertTrue($student->isTourComplete());
    }
}
