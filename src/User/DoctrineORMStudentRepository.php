<?php

namespace PhpSchool\Website\User;

use Doctrine\ORM\EntityRepository;
use PhpSchool\Website\User\Entity\Student;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends EntityRepository<Student>
 */
class DoctrineORMStudentRepository extends EntityRepository implements StudentRepository
{
    public function findById(UuidInterface $id): ?Student
    {
        return $this->find($id->toString());
    }

    public function update(Student $student): void
    {
        $this->getEntityManager()->persist($student);
        $this->getEntityManager()->flush();
    }
}
