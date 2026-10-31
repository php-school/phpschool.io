<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\User\StudentDTO;

class PathGenerator
{
    public function random(StudentDTO $student): string
    {
        return Path::join($this->student($student), bin2hex(random_bytes(6)));
    }

    /**
     * Holds all of a student's uploads, eg. /tmp/php-school/<student id>
     */
    public function student(StudentDTO $student): string
    {
        return System::tempDir($student->id->toString());
    }
}
