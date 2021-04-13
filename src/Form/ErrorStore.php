<?php

namespace PhpSchool\Website\Form;

use AdamWathan\Form\ErrorStore\ErrorStoreInterface;

class ErrorStore implements ErrorStoreInterface
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasError($key)
    {
        return isset($this->errors[$key]);
    }

    /**
     * @param string $key
     * @return string
     */
    public function getError($key)
    {
        return array_values($this->errors[$key])[0];
    }
}
