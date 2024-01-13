<?php

namespace PhpSchool\Website\Online;

use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\User\StudentDTO;

class PathGenerator
{
    public function random(StudentDTO $student): string
    {
        return System::tempDir(
            Path::join($student->id->toString(), bin2hex(random_bytes(6)))
        );
    }
}
