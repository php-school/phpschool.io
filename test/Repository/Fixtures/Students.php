<?php

namespace PhpSchool\WebsiteTest\Repository\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use PhpSchool\Website\User\Entity\Student;
use Ramsey\Uuid\UuidInterface;

class Students implements FixtureInterface
{
    public UuidInterface $student1Id;

    public function load(ObjectManager $manager)
    {
        $manager->persist(
            $s = new Student('GH1', 'Student 1', 'student1@phpschool.com', 'Student 1', null, null)
        );

        $this->student1Id = $s->getId();

        $manager->persist(
            new Student('GH2', 'Student 2', 'student2@phpschool.com', 'Student 2', null, null)
        );

        $manager->persist(
            new Student('GH3', 'Student 3', 'student3@phpschool.com', 'Student 2', null, null)
        );

        $manager->flush();
    }
}
