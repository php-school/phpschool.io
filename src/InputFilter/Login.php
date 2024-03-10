<?php

declare(strict_types=1);

namespace PhpSchool\Website\InputFilter;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\StringLength;

/**
 * @phpstan-type LoginData array{
 *      email: string,
 *      password: string,
 *  }
 * @extends InputFilter<LoginData>
 */
class Login extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 254,
                        'break_chain_on_failure' => true,
                    ]
                ],
                [
                    'name' => EmailAddress::class,
                ]
            ]
        ]);

        $this->add([
            'name' => 'password',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                    ]
                ]
            ]
        ]);
    }
}
