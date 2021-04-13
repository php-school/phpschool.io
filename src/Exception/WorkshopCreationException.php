<?php

namespace PhpSchool\Website\Exception;

use RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopCreationException extends RuntimeException
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
