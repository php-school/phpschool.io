<?php

namespace PhpSchool\Website\Form;

use PhpSchool\Website\User\Session;
use Laminas\InputFilter\InputFilterInterface;

class FormHandlerFactory
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function create(InputFilterInterface $inputFilter): FormHandler
    {
        return new FormHandler($inputFilter, $this->session);
    }
}
