<?php

namespace PhpSchool\Website\InputFilter;

use Github\Client;
use PhpSchool\Website\Repository\WorkshopRepository;
use Zend\InputFilter\InputFilter;
use Zend\Validator\Callback;
use Zend\Validator\EmailAddress;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class SubmitWorkshop extends InputFilter
{
    private static $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d-\.]+)\/?$/';

    public function __construct(Client $gitHubClient, WorkshopRepository $workshopRepository)
    {
        $this->add([
            'name' => 'github-url',
            'required' => true,
            'validators' => [
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => static::$gitHubRepoUrlRegex,
                        'messages' => [
                            Regex::NOT_MATCH => 'The URL "%value%" is not a valid GitHub repository URL.',
                        ],
                        'break_chain_on_failure' => true,
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function ($url) {
                            preg_match(static::$gitHubRepoUrlRegex, $url, $matches);
                            $owner = $matches[3];
                            $repo = $matches[4];

                            return (bool) @file_get_contents(sprintf(static::$gitHubComposerJsonUrlFormat, $owner, $repo));
                        },
                        'messages' => [
                            Callback::INVALID_VALUE => 'Cannot download the contents of composer.json from "%value%". Does it exist?',
                        ],
                        'break_chain_on_failure' => true,
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function ($url) use ($gitHubClient) {
                            preg_match(static::$gitHubRepoUrlRegex, $url, $matches);
                            $owner = $matches[3];
                            $repo = $matches[4];

                            return count($gitHubClient->api('repo')->tags($owner, $repo)) > 0;
                        },
                        'messages' => [
                            Callback::INVALID_VALUE => 'Cannot find any git tags in "%value%". Make sure you tag a release.',
                        ],
                        'break_chain_on_failure' => true,
                    ]
                ]
            ]
        ]);

        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                [
                    'name' => EmailAddress::class
                ]
            ],
        ]);

        $this->add([
            'name' => 'name',
            'required' => true,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_LONG => 'Name should be between %min% and %max% characters long.',
                            StringLength::TOO_SHORT => 'Name should be between %min% and %max% characters long.'
                        ]
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'contact',
            'required' => false,
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 512,
                        'messages' => [
                            StringLength::TOO_LONG => 'Contact should be between %min% and %max% characters long.',
                            StringLength::TOO_SHORT => 'Contact should be between %min% and %max% characters long.'
                        ]
                    ]
                ]
            ],
        ]);

        $this->add([
            'name' => 'workshop-name',
            'required' => true,
            'validators' => [
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function ($name) use ($workshopRepository) {
                            try {
                                $workshopRepository->findByDisplayName($name);
                            } catch (\RuntimeException $e) {
                                return true;
                            }
                            return false;
                        },
                        'messages' => [
                            Callback::INVALID_VALUE => 'The name is used by an existing workshop, please try another.'
                        ]
                    ]
                ],
                [
                    'name' => StringLength::class,
                    'options' => [
                        'min' => 2,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_LONG => 'Workshop name should be between %min% and %max% characters long.',
                            StringLength::TOO_SHORT => 'Workshop name should be between %min% and %max% characters long.'
                        ]
                    ]
                ]
            ],
        ]);
    }
}
