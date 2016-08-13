<?php

namespace PhpSchool\Website;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Repository\WorkshopRepository;
use RuntimeException;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class WorkshopFeed
{

    /**
     * @var WorkshopRepository
     */
    private $workshopRepository;

    /**
     * @var string
     */
    private $outputFile;

    public function __construct(WorkshopRepository $workshopRepository, string $outputFile)
    {
        $this->workshopRepository = $workshopRepository;
        $this->outputFile = $outputFile;
    }

    public function generate()
    {
        $workshops = $this->workshopRepository->findBy(['approved' => true]);

        $workshops = ['workshops' => collect($workshops)
            ->map(function (Workshop $workshop) {
                return $workshop->toArray();
            })
            ->all()];

        $result = file_put_contents($this->outputFile, json_encode($workshops, JSON_PRETTY_PRINT));

        if (!$result) {
            throw new RuntimeException(sprintf('File: "%s" could not be written', $this->outputFile));
        }
    }
}
