<?php

namespace PhpSchool\WebsiteTest\Repository;

use PhpSchool\Website\Entity\Event;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\WebsiteTest\Repository\Fixtures\Events;
use PhpSchool\WebsiteTest\Repository\Fixtures\Students;

class DoctrineORMStudentRepositoryTest extends DoctrineORMRepositoryTest
{
    public function testFindById(): void
    {
        $this->loadFixture($fixture = new Students());

        $this->assertEquals(
            'Student 1',
            $this->getRepository(Student::class)->findById($fixture->student1Id)->getName()
        );
    }

    public function testUpdate(): void
    {
        $this->loadFixture($fixture = new Students());

        $repo = $this->getRepository(Student::class);

        $student = $this->getRepository(Student::class)->findById($fixture->student1Id);
        $student->setName('Jamiraqui');

        $repo->update($student);

        $student = $this->getRepository(Student::class)->findById($fixture->student1Id);
        $this->assertEquals('Jamiraqui', $student->getName());
    }
}
