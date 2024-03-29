<?php

declare(strict_types=1);

namespace PhpSchool\Website\InputFilter;

use Github\Client;
use GuzzleHttp\Exception\TransferException;
use PhpSchool\Website\Repository\WorkshopRepository;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator\Callback;
use Laminas\Validator\EmailAddress;
use Laminas\Validator\Regex;
use Laminas\Validator\StringLength;

/**
 * @phpstan-type SubmitWorkshopData array{
 *      github-url: string,
 *      email: string,
 *      name: string,
 *      contact?: string,
 *      workshop-name: string,
 *  }
 * @extends InputFilter<SubmitWorkshopData>
 */
class SubmitWorkshop extends InputFilter
{
    private static string $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static string $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d\-\.]+)\/?$/';

    public function __construct(Client $gitHubClient, WorkshopRepository $workshopRepository)
    {
        $this->add([
            'name' => 'github-url',
            'required' => true,
            'validators' => [
                [
                    'name' => Regex::class,
                    'options' => [
                        'pattern' => self::$gitHubRepoUrlRegex,
                        'messages' => [
                            Regex::NOT_MATCH => 'The URL "%value%" is not a valid GitHub repository URL.',
                        ],
                        'break_chain_on_failure' => true,
                    ]
                ],
                [
                    'name' => Callback::class,
                    'options' => [
                        'callback' => function (string $url) {
                            preg_match(self::$gitHubRepoUrlRegex, $url, $matches);
                            $owner = $matches[3];
                            $repo = $matches[4];

                            try {
                                $response = (new \GuzzleHttp\Client())
                                    ->request('GET', sprintf(self::$gitHubComposerJsonUrlFormat, $owner, $repo));
                                return $response->getStatusCode() === 200;
                            } catch (TransferException $e) {
                                return false;
                            }
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
                        'callback' => function (string $url) use ($gitHubClient) {
                            preg_match(self::$gitHubRepoUrlRegex, $url, $matches);
                            $owner = $matches[3];
                            $repository = $matches[4];

                            /** @var \Github\Api\Repo $repo */
                            $repo = $gitHubClient->api('repo');

                            return count($repo->tags($owner, $repository)) > 0;
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
                        'callback' => function (string $name) use ($workshopRepository) {
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
