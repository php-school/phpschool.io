<?php

namespace PhpSchool\Website\Validator;

use Github\Client;
use PhpSchool\Website\Repository\WorkshopRepository;
use Zend\InputFilter\Input;
use Zend\Validator\Callback;
use Zend\Validator\EmailAddress;
use Zend\Validator\Regex;
use Zend\Validator\StringLength;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class SubmitWorkshop extends Validator
{
    private static $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d-]+)\/?$/';

    public function __construct(Client $gitHubClient, WorkshopRepository $workshopRepository)
    {
        $regex = (new Regex(static::$gitHubRepoUrlRegex))
            ->setMessage('The URL "%value%" is not a valid GitHub repository URL.', Regex::NOT_MATCH);

        $composerJsonPresence = new Callback(function ($url) {
            preg_match(static::$gitHubRepoUrlRegex, $url, $matches);
            $owner = $matches[3];
            $repo = $matches[4];

            return (bool) @file_get_contents(sprintf(static::$gitHubComposerJsonUrlFormat, $owner, $repo));
        });
        $composerJsonPresence
            ->setMessage('Cannot download the contents of composer.json from %value%. Does it exist?', Callback::INVALID_VALUE);

        $tags = new Callback(function ($url) {
            preg_match(static::$gitHubRepoUrlRegex, $url, $matches);
            $owner = $matches[3];
            $repo = $matches[4];

            return count((new Client)->api('repo')->tags($owner, $repo)) > 0;
        });
        $tags
            ->setMessage('Cannot find any git tags in "%value%". Make sure you tag a release.', Callback::INVALID_VALUE);

        $gitHubUrl = new Input('github-url');
        $gitHubUrl->getValidatorChain()
            ->attach($regex, true)
            ->attach($composerJsonPresence, true)
            ->attach($tags, true);
        
        $this->add($gitHubUrl);

        $email = new Input('email');
        $email->getValidatorChain()
            ->attach(new EmailAddress);

        $this->add($email);

        $uniqueNameValidator = new Callback(function ($name) use ($workshopRepository) {
            try {
                $workshopRepository->findByDisplayName($name);
            } catch (\RuntimeException $e) {
                return true;
            }
            return false;
        });
        $uniqueNameValidator
            ->setMessage('The name is used by an existing workshop, please try another.', Callback::INVALID_VALUE);

        $name = new Input('name');
        $name->getValidatorChain()
            ->attach(new StringLength(['min' => 1, 'max' => 255]))
            ->attach($uniqueNameValidator);

        $this->add($name);
    }
}
