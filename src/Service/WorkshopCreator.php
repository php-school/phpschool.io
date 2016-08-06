<?php

namespace PhpSchool\Website\Service;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\Validator\WorkshopComposerJson as WorkshopComposerJsonValidator;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopCreator
{
    private static $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d-]+)\/?$/';

    /**
     * @var WorkshopComposerJsonValidator
     */
    private $composerJsonValidator;

    /**
     * @var WorkshopRepository
     */
    private $workshopRepository;

    public function __construct(
        WorkshopComposerJsonValidator $composerJsonValidator,
        WorkshopRepository $workshopRepository
    ) {
        $this->composerJsonValidator = $composerJsonValidator;
        $this->workshopRepository = $workshopRepository;
    }

    public function create(array $data) : Workshop
    {
        preg_match(static::$gitHubRepoUrlRegex, $data['github-url'], $matches);
        $owner  = $matches[3];
        $repo   = $matches[4];

        if (!$this->composerJsonValidator->validateArray($this->getComposerJsonContents($owner, $repo))) {
            throw new WorkshopCreationException($this->composerJsonValidator->getMessages());
        }

        $jsonData = $this->composerJsonValidator->getValues();

        $workshop = new Workshop(
            $owner,
            $repo,
            basename(array_values($jsonData['bin'])[0]),
            $data['name'],
            $jsonData['description']
        );

        $this->workshopRepository->save($workshop);
        return $workshop;
    }

    /**
     * @param string $owner
     * @param string $repo
     * @return array
     */
    private function getComposerJsonContents(string $owner, string $repo)
    {
        $url = sprintf(static::$gitHubComposerJsonUrlFormat, $owner, $repo);
        $composerJson = file_get_contents($url);
        return json_decode($composerJson, true);
    }
}
