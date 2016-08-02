<?php

namespace PhpSchool\Website\Validator;

use Zend\InputFilter\Input;
use Zend\Validator\EmailAddress;
use Zend\Validator\StringLength;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Login extends Validator
{

    public function __construct()
    {
        $email = new Input('email');
        $email->getValidatorChain()
            ->attach(new EmailAddress)
            ->attach(new StringLength(['min' => 3, 'max' => 254]));

        $this->add($email);

        $password = new Input('password');
        $password->getValidatorChain()
            ->attach(new StringLength(['min' => 1, 'max' => 255]));

        $this->add($password);
    }
}
