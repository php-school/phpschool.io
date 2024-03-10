<?php

namespace PhpSchool\Website\Service;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Exception\WorkshopCreationException;
use PhpSchool\Website\Repository\WorkshopRepository;
use PhpSchool\Website\InputFilter\WorkshopComposerJson as WorkshopComposerJsonInputFilter;

class WorkshopCreator
{
    private static string $gitHubComposerJsonUrlFormat = 'https://raw.githubusercontent.com/%s/%s/master/composer.json';
    private static string $gitHubRepoUrlRegex = '/^(https?:\/\/)?(www.)?github.com\/([A-Za-z\d-]+)\/([A-Za-z\d-]+)\/?$/';

    private WorkshopComposerJsonInputFilter $composerJsonInputFilter;
    private WorkshopRepository $workshopRepository;

    public function __construct(
        WorkshopComposerJsonInputFilter $composerJsonInputFilter,
        WorkshopRepository $workshopRepository
    ) {
        $this->composerJsonInputFilter = $composerJsonInputFilter;
        $this->workshopRepository = $workshopRepository;
    }

    /**
     * @param  array{
     *     workshop-name: string,
     *     email: string,
     *     name: string,
     *     contact?: string,
     *     github-url: string
     * } $data
     * @return Workshop
     */
    public function create(array $data): Workshop
    {
        preg_match(self::$gitHubRepoUrlRegex, $data['github-url'], $matches);
        $owner  = $matches[3];
        $repo   = $matches[4];

        $this->composerJsonInputFilter->setData($this->getComposerJsonContents($owner, $repo));

        if (!$this->composerJsonInputFilter->isValid()) {
            throw new WorkshopCreationException($this->composerJsonInputFilter->getMessages());
        }

        /** @var array{bin: array<string>, description: string} $jsonData */
        $jsonData = $this->composerJsonInputFilter->getValues();

        $workshop = new Workshop(
            $owner,
            $repo,
            basename(array_values($jsonData['bin'])[0]),
            $data['workshop-name'],
            $jsonData['description'],
            $data['email'],
            $data['name'],
            $data['contact'] ?? null
        );

        $this->workshopRepository->save($workshop);
        return $workshop;
    }

    /**
     * @return array<mixed>
     */
    private function getComposerJsonContents(string $owner, string $repo): array
    {
        return (array) json_decode(
            file_get_contents(sprintf(self::$gitHubComposerJsonUrlFormat, $owner, $repo)) ?: '',
            true
        );
    }
}
