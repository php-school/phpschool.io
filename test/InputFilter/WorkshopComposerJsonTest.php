<?php

namespace PhpSchool\WebsiteTest\InputFilter;

use PhpSchool\Website\InputFilter\WorkshopComposerJson;
use PHPUnit\Framework\TestCase;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopComposerJsonTest extends TestCase
{
    /**
     * @param array $input
     * @param array $output
     * @param array $messages
     * @dataProvider inputDataProvider
     */
    public function testInputFilter(array $input, array $output = null, array $messages = null)
    {
        $filter = new WorkshopComposerJson;
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
                    'type'        => 'php-school-workshop',
                    'bin'         => ['bin/learnyouphp'],
                    'description' => 'Workshop description',
                ],
                [
                    'type'        => 'php-school-workshop',
                    'bin'         => ['bin/learnyouphp'],
                    'description' => 'Workshop description',
                ],
            ],
            'invalid-missing-fields' => [
                [],
                null,
                [
                    'type'        => ['isEmpty' => 'The "type" key in "composer.json" should be "php-school-workshop"'],
                    'bin'         => ['isEmpty' => 'The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}'],
                    'description' => ['isEmpty' => 'The "description" key in "composer.json" should be set with a small description of the workshop'],
                ]
            ],
            'invalid-missing-fields' => [
                [
                    'type'        => 'incorrect-type',
                    'bin'         => ['bin/learnyouphp'],
                    'description' => 'Workshop description',
                ],
                null,
                [
                    'type' => [
                        'notSame' => 'The "type" key in "composer.json" should be "php-school-workshop"'
                    ],
                ]
            ],
            'invalid-bin-format' => [
                [
                    'type'        => 'php-school-workshop',
                    'bin'         => ['one', 'two'],
                    'description' => 'Workshop description',
                ],
                null,
                [
                    'bin' => [
                        'callbackValue' => 'The "bin" key in "composer.json" should be an array with one entry which points to the entry point of the workshop. For example: {"bin" : ["bin/learnyouphp"]}'
                    ],
                ]
            ],
            'invalid-description-too-short' => [
                [
                    'type'        => 'php-school-workshop',
                    'bin'         => ['bin/learnyouphp'],
                    'description' => 'Workshop',
                ],
                null,
                [
                    'description' => [
                        'stringLengthTooShort' => 'The "description" key should be between 10 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-description-too-long' => [
                [
                    'type'        => 'php-school-workshop',
                    'bin'         => ['bin/learnyouphp'],
                    'description' => str_repeat('A', 513),
                ],
                null,
                [
                    'description' => [
                        'stringLengthTooLong' => 'The "description" key should be between 10 and 512 characters long.'
                    ]
                ]
            ],
        ];
    }
}
