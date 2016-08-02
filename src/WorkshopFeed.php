<?php

namespace PhpSchool\Website;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Repository\WorkshopRepository;

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

        $workshops = collect($workshops)
            ->map(function (Workshop $workshop) {
                return json_encode($workshop);
            })
            ->all();

        $result = file_put_contents($this->outputFile, json_encode($workshops, JSON_PRETTY_PRINT));

        if (!$result) {
            //do something
        }
    }
}
