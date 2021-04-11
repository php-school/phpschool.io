<?php

namespace PhpSchool\WebsiteTest\InputFilter;

use PhpSchool\Website\InputFilter\Event;
use PHPUnit\Framework\TestCase;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class EventTest extends TestCase
{
    /**
     * @param array $input
     * @param array $output
     * @param array $messages
     * @dataProvider inputDataProvider
     */
    public function testInputFilter(array $input, array $output = null, array $messages = null)
    {
        $filter = new Event;
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
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                    'poster'      => null,
                ],
            ],
            'invalid-missing-fields' => [
                [],
                null,
                [
                    'name'          => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'description'   => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'date'          => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'venue'         => ['isEmpty' => 'Value is required and can\'t be empty'],
                ]
            ],
            'invalid-name-too-short' => [
                [
                    'name'        => 'My',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'name' => [
                        'stringLengthTooShort' => 'Title should be between 5 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-name-too-long' => [
                [
                    'name'        => str_repeat('A', 256),
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'name' => [
                        'stringLengthTooLong' => 'Title should be between 5 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-description-too-short' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'description' => [
                        'stringLengthTooShort' => 'Description should be between 10 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-description-too-long' => [
                [
                    'name'        => 'My Event',
                    'description' => str_repeat('A', 513),
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'description' => [
                        'stringLengthTooLong' => 'Description should be between 10 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-link' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://some link/',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'link' => [
                        'notUri' => 'The input does not appear to be a valid Uri'
                    ]
                ]
            ],
            'invalid-link-too-short' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'h',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'link' => [
                        'stringLengthTooShort' => 'Link should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-link-too-long' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => str_repeat('A', 256),
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'link' => [
                        'stringLengthTooLong' => 'Link should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-date' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:0',
                    'venue'       => 'Some Venue',
                ],
                null,
                [
                    'date' => [
                        'dateInvalidDate' => 'The input does not appear to be a valid date'
                    ]
                ]
            ],
            'invalid-venue-too-short' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => 'Venue',
                ],
                null,
                [
                    'venue' => [
                        'stringLengthTooShort' => 'Venue should be between 10 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-venue-too-long' => [
                [
                    'name'        => 'My Event',
                    'description' => 'My description',
                    'link'        => 'http://www.meetup.com',
                    'date'        => '2016-10-01T10:00',
                    'venue'       => str_repeat('A', 513),
                ],
                null,
                [
                    'venue' => [
                        'stringLengthTooLong' => 'Venue should be between 10 and 512 characters long.'
                    ]
                ]
            ],
        ];
    }
}
