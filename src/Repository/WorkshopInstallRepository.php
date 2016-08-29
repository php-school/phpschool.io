<?php
namespace PhpSchool\Website\Repository;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
interface WorkshopInstallRepository
{
    public function totalInstallsInLast30Days(Workshop $workshop) : int;

    public function totalInstalls(Workshop $workshop) : int;

    public function findInstallsInLast30Days(Workshop $workshop): array;

    public function save(WorkshopInstall $workshopInstall);

    public function removeAllByWorkshop(Workshop $workshop);
}
