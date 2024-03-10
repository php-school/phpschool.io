<?php

namespace PhpSchool\Website\Exception;

use RuntimeException;

class WorkshopCreationException extends RuntimeException
{
    /**
     * @param array<string, array<array<mixed>|string>> $errors
     */
    public function __construct(private array $errors)
    {
    }

    /**
     * @return array<string, array<array<mixed>|string>>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
