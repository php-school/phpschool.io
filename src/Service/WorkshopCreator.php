<?php

namespace PhpSchool\Website\Service;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopCreator
{
    private static $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d-]+)\/?$/';

    /**
     * @var WorkshopComposerJsonInputFilter
     */
    private $composerJsonInputFilter;

    /**
     * @var WorkshopRepository
     */
    private $workshopRepository;

    public function __construct(
        WorkshopComposerJsonInputFilter $composerJsonInputFilter,
        WorkshopRepository $workshopRepository
    ) {
        $this->composerJsonInputFilter = $composerJsonInputFilter;
        $this->workshopRepository = $workshopRepository;
    }

    public function create(array $data): Workshop
    {
        preg_match(static::$gitHubRepoUrlRegex, $data['github-url'], $matches);
        $owner  = $matches[3];
        $repo   = $matches[4];

        $this->composerJsonInputFilter->setData($this->getComposerJsonContents($owner, $repo));

        if (!$this->composerJsonInputFilter->isValid()) {
            throw new WorkshopCreationException($this->composerJsonValidator->getMessages());
        }

        $jsonData = $this->composerJsonInputFilter->getValues();

        $workshop = new Workshop(
            $owner,
            $repo,
            basename(array_values($jsonData['bin'])[0]),
            $data['workshop-name'],
            $jsonData['description'],
            $data['email'],
            $data['name'],
            $data['contact']
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
        return json_decode(file_get_contents(sprintf(static::$gitHubComposerJsonUrlFormat, $owner, $repo)), true);
    }
}
