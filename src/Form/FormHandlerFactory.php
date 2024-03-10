<?php

namespace PhpSchool\Website\Form;

use Laminas\InputFilter\InputFilter;
use PhpSchool\Website\User\Session;
use Laminas\InputFilter\InputFilterInterface;

class FormHandlerFactory
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function create(InputFilter $inputFilter): FormHandler /** @phpstan-ignore-line  */
    {
        return new FormHandler($inputFilter, $this->session);
    }
}
