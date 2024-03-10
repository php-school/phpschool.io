<?php

declare(strict_types=1);

namespace PhpSchool\Website\User;

use PhpSchool\Website\User\Entity\Student;
use Ramsey\Uuid\UuidInterface;

interface StudentRepository
{
    public function findById(UuidInterface $id): ?Student;

    public function update(Student $student): void;
}
