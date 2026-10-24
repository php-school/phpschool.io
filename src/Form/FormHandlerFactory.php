<?php

declare(strict_types=1);

namespace PhpSchool\Website\Form;

use Laminas\InputFilter\InputFilter;
use PhpSchool\Website\User\Session;

class FormHandlerFactory
{
    public function __construct(
        private readonly Session $session,
    ) {}

    public function create(InputFilter $inputFilter): FormHandler /** @phpstan-ignore-line  */
    {
        return new FormHandler($inputFilter, $this->session);
    }
}
