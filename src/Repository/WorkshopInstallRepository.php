<?php

declare(strict_types=1);

namespace PhpSchool\Website\Repository;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;

interface WorkshopInstallRepository
{
    public function totalInstallsInLast30Days(Workshop $workshop): int;

    public function totalInstalls(Workshop $workshop): int;

    /**
     * @return array<WorkshopInstall>
     */
    public function findInstallsInLast30Days(Workshop $workshop): array;

    public function save(WorkshopInstall $workshopInstall): void;

    public function removeAllByWorkshop(Workshop $workshop): void;
}
