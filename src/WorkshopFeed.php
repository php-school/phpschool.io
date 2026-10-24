<?php

declare(strict_types=1);

namespace PhpSchool\Website;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Repository\WorkshopRepository;
use RuntimeException;

class WorkshopFeed
{
    public function __construct(
        private readonly WorkshopRepository $workshopRepository,
        private readonly string $outputFile,
    ) {}

    public function generate(): void
    {
        $workshops = $this->workshopRepository->findAllApproved();

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
