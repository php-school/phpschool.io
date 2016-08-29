<?php

namespace PhpSchool\Website\Form;

use RKA\Session;
use Zend\InputFilter\InputFilterInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class FormHandlerFactory
{
    /**
     * @var Session
     */
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function create(InputFilterInterface $inputFilter)
    {
        return new FormHandler($inputFilter, $this->session);
    }
}
