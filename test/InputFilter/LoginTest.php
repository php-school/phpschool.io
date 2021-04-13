<?php

namespace PhpSchool\WebsiteTest\InputFilter;

use PhpSchool\Website\InputFilter\Login;
use PHPUnit\Framework\TestCase;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class LoginTest extends TestCase
{
    /**
     * @param array $input
     * @param array $output
     * @param array $messages
     * @dataProvider inputDataProvider
     */
    public function testInputFilter(array $input, array $output = null, array $messages = null)
    {
        $filter = new Login();
        $filter->setData($input);
        if ($output === null) {
            $this->assertFalse($filter->isValid(), 'Input must not be valid');
            $this->assertEquals($messages, $filter->getMessages());
        } else {
            $this->assertTrue($filter->isValid(), 'Input must be valid. Errors:' . json_encode($filter->getMessages()));
            $this->assertEquals($output, $filter->getValues());
        }
    }

    public function inputDataProvider()
    {
        return [
            'completely-valid-input' => [
                [
                    'email'         => 'aydin@hotmail.co.uk',
                    'password'      => 'mypassword',
                ],
                [
                    'email'         => 'aydin@hotmail.co.uk',
                    'password'      => 'mypassword',
                ],
            ],
            'invalid-missing-fields' => [
                [],
                null,
                [
                    'email'    => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'password' => ['isEmpty' => 'Value is required and can\'t be empty'],
                ]
            ],
            'invalid-email-too-short' => [
                [
                    'email'    => 'a@',
                    'password' => 'mypassword',
                ],
                null,
                [
                    'email' => [
                        'stringLengthTooShort' => 'The input is less than 3 characters long'
                    ]
                ]
            ],
            'invalid-email-too-long' => [
                [
                    'email'    => str_repeat('@', 255),
                    'password' => 'mypassword',
                ],
                null,
                [
                    'email' => [
                        'stringLengthTooLong' => 'The input is more than 254 characters long'
                    ]
                ]
            ],
            'invalid-email' => [
                [
                    'email'    => 'axxxfxf',
                    'password' => 'mypassword',
                ],
                null,
                [
                    'email' => [
                        'emailAddressInvalidFormat' => 'The input is not a valid email address. Use the basic format local-part@hostname'
                    ]
                ]
            ],
            'invalid-password-too-short' => [
                [
                    'email'    => 'aydin@hotmail.co.uk',
                    'password' => 'my',
                ],
                null,
                [
                    'password' => [
                        'stringLengthTooShort' => 'The input is less than 3 characters long'
                    ]
                ]
            ],
            'invalid-password-too-long' => [
                [
                    'email'    => 'aydin@hotmail.co.uk',
                    'password' => str_repeat('A', 256),
                ],
                null,
                [
                    'password' => [
                        'stringLengthTooLong' => 'The input is more than 255 characters long'
                    ]
                ]
            ],
        ];
    }
}
