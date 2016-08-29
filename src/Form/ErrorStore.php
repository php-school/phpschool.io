<?php

namespace PhpSchool\Website\Form;

use AdamWathan\Form\ErrorStore\ErrorStoreInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ErrorStore implements ErrorStoreInterface
{
    /**
     * @var array
     */
    private $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    public function hasError($key)
    {
        return isset($this->errors[$key]);
    }

    public function getError($key)
    {
        return array_values($this->errors[$key])[0];
    }
}
