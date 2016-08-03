<?php

namespace PhpSchool\Website;

use PhpSchool\Website\Entity\Workshop;
use PhpSchool\Website\Entity\WorkshopInstall;
use PhpSchool\Website\Repository\WorkshopInstallRepository;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DownloadManager
{
    /**
     * @var WorkshopInstallRepository
     */
    private $repository;

    public function __construct(WorkshopInstallRepository $repository)
    {
        $this->repository = $repository;
    }

    public function totalInstallsInLast30Days(Workshop $workshop) : int
    {
        return $this->repository->totalInstallsInLast30Days($workshop);
    }

    public function totalInstalls(Workshop $workshop) : int
    {
        return $this->repository->totalInstalls($workshop);
    }

    public function addInstall(Workshop $workshop, string $ipAddress, string $version)
    {
        $this->repository->save(new WorkshopInstall($workshop, $ipAddress, $version));
    }
}
