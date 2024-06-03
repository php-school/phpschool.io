<?php

declare(strict_types=1);

namespace PhpSchool\WebsiteTest\User;

use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\StudentDTO;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class StudentDTOTest extends TestCase
{
    public function testSerialize(): void
    {
        $state = new StudentCloudState([]);

        $student = new StudentDTO(
            Uuid::uuid4(),
            'Student 1',
            'student@phpschool.com',
            'Student 1',
            null,
            null,
            new \DateTime('14 February 2022'),
            false,
            $state
        );

        $this->assertEquals(
            [
                'username' => 'Student 1',
                'email' => 'student@phpschool.com',
                'name' => 'Student 1',
                'profile_picture' => null,
                'location' => null,
                'join_date' => 'February 2022',
                'tour_complete' => false,
                'state' => $state->jsonSerialize()
            ],
            $student->jsonSerialize()
        );
    }
}
