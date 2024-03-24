<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\Cloud;

use PhpSchool\Website\Online\StudentCloudState;
use PHPUnit\Framework\TestCase;

class StudentCloudStateTest extends TestCase
{
    public function testCount(): void
    {
        $state = new StudentCloudState([
            'Workshop 1' => [
                'completedExercises' => [
                    "Exercise 1",
                    "Exercise 2",
                ]
            ],
            'Workshop 2' => [
                'completedExercises' => [
                    "Exercise 1",
                    "Exercise 2",
                    "Exercise 3",
                ]
            ]
        ]);

        $this->assertEquals(5, $state->getTotalCompletedExercises());
    }

    public function testJsonSerialize(): void
    {
        $stateData = [
            'Workshop 1' => [
                'completedExercises' => [
                    "Exercise 1",
                    "Exercise 2",
                ]
            ],
            'Workshop 2' => [
                'completedExercises' => [
                    "Exercise 1",
                    "Exercise 2",
                    "Exercise 3",
                ]
            ]
        ];

        $state = new StudentCloudState($stateData);

        $this->assertEquals(
            [
                'workshops' => $stateData,
                'total_completed' => 5
            ],
            $state->jsonSerialize()
        );
    }
}
