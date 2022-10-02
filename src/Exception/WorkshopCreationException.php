<?php

namespace PhpSchool\Website\Exception;

use RuntimeException;

class WorkshopCreationException extends RuntimeException
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
