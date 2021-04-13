<?php

namespace PhpSchool\WebsiteTest\InputFilter;

use Github\Api\Repo;
use Github\Client;
use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\InputFilter\SubmitWorkshop;
use PhpSchool\Website\Repository\WorkshopRepository;
use PHPUnit\Framework\TestCase;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class SubmitWorkshopTest extends TestCase
{
    /**
     * @param array $input
     * @param array $output
     * @param array $messages
     * @dataProvider inputDataProvider
     */
    public function testInputFilter(array $input, array $output = null, array $messages = null)
    {
        $repo = $this->createMock(Repo::class);
        $repo->expects($this->any())->method('tags')->willReturnCallback(function ($owner, $repo) {
            if ($owner === 'php-school' && $repo === 'phpschool.io') {
                return [];
            }
            return ['got-tags-yo'];
        });
        $client = $this->createMock(Client::class);
        $client->expects($this->any())->method('api')->willReturn($repo);

        $workshopRepo = $this->createMock(WorkshopRepository::class);
        $workshopRepo
            ->expects($this->any())
            ->method('findByDisplayName')
            ->willReturnCallback(function ($displayName) {
                if ($displayName === 'Learn You PHP!') {
                    return $this->createMock(Workshop::class);
                }
                throw new \RuntimeException();
            });

        $filter = new SubmitWorkshop($client, $workshopRepo);
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
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
            ],
            'completely-valid-input-missing-contact' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'workshop-name' => 'PHP7',
                ],
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => null,
                    'workshop-name' => 'PHP7',
                ],
            ],
            'invalid-github-url' => [
                [
                    'github-url'    => 'https://www.google.com',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'github-url' => [
                        'regexNotMatch' => 'The URL "https://www.google.com" is not a valid GitHub repository URL.'
                    ]
                ]
            ],
            'invalid-no-composer.json' => [
                [
                    'github-url'    => 'https://github.com/AydinHassan/private',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'github-url' => [
                        'callbackValue' => 'Cannot download the contents of composer.json from "https://github.com/AydinHassan/private". Does it exist?'
                    ]
                ]
            ],
            'invalid-no-git-tags' => [
                [
                    'github-url'    => 'https://github.com/php-school/phpschool.io',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'github-url' => [
                        'callbackValue' => 'Cannot find any git tags in "https://github.com/php-school/phpschool.io". Make sure you tag a release.'
                    ]
                ]
            ],
            'invalid-email' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'email' => [
                        'emailAddressInvalidFormat' => 'The input is not a valid email address. Use the basic format local-part@hostname'
                    ]
                ]
            ],
            'invalid-name-too-short' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'A',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'name' => [
                        'stringLengthTooShort' => 'Name should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-name-too-long' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => str_repeat('A', 256),
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'name' => [
                        'stringLengthTooLong' => 'Name should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-contact-too-short' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'A',
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'contact' => [
                        'stringLengthTooShort' => 'Contact should be between 2 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-contact-too-long' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => str_repeat('A', 513),
                    'workshop-name' => 'PHP7',
                ],
                null,
                [
                    'contact' => [
                        'stringLengthTooLong' => 'Contact should be between 2 and 512 characters long.'
                    ]
                ]
            ],
            'invalid-workshop-name-too-short' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'A',
                ],
                null,
                [
                    'workshop-name' => [
                        'stringLengthTooShort' => 'Workshop name should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-workshop-name-too-short' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => str_repeat('A', 513),
                ],
                null,
                [
                    'workshop-name' => [
                        'stringLengthTooLong' => 'Workshop name should be between 2 and 255 characters long.'
                    ]
                ]
            ],
            'invalid-workshop-name-not-unique' => [
                [
                    'github-url'    => 'https://github.com/php-school/learn-you-php',
                    'email'         => 'aydin@hotmail.co.uk',
                    'name'          => 'Aydin Hassan',
                    'contact'       => 'https://twitter.com/aydin_h',
                    'workshop-name' => 'Learn You PHP!',
                ],
                null,
                [
                    'workshop-name' => [
                        'callbackValue' => 'The name is used by an existing workshop, please try another.'
                    ]
                ]
            ],
            'invalid-missing-fields' => [
                [],
                null,
                [
                    'github-url'    => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'email'         => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'name'          => ['isEmpty' => 'Value is required and can\'t be empty'],
                    'workshop-name' => ['isEmpty' => 'Value is required and can\'t be empty'],
                ]
            ],
        ];
    }
}
