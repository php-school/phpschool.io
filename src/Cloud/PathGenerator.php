<?php

namespace PhpSchool\Website\Cloud;

use PhpSchool\PhpWorkshop\Utils\System;

class PathGenerator
{
    public function random(): string
    {
        return System::tempDir(bin2hex(random_bytes(6)));
    }
}
