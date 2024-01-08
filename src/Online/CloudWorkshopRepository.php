<?php

namespace PhpSchool\Website\Online;

use Composer\InstalledVersions;
use PhpSchool\Website\Repository\WorkshopRepository;
use RuntimeException;

class CloudWorkshopRepository
{
    private WorkshopRepository $workshopRepository;

    public function __construct(WorkshopRepository $workshopRepository)
    {
        $this->workshopRepository = $workshopRepository;
    }

    public function findByCode(string $code): CloudInstalledWorkshop
    {
        foreach ($this->findAll() as $workshop) {
            if ($workshop->getCode() === $code) {
                return  $workshop;
            }
        }

        throw new RuntimeException(sprintf('Cannot find workshop with code: "%s"', $code));
    }

    /**
     * @return array<CloudInstalledWorkshop>
     */
    public function findAll(): array
    {
        $workshops = $this->getComposerInstalledWorkshops();

        return array_map(
            function (string $packageName) {
                /** @var string $path */
                $path = InstalledVersions::getInstallPath($packageName);
                $path = realpath($path);
                $workshop = $this->workshopRepository->findByCode(
                    $this->getWorkshopCode($path)
                );

                return new CloudInstalledWorkshop(
                    include $path . '/app/bootstrap.php',
                    $workshop
                );
            },
            $workshops,
        );
    }

    public function totalExerciseCount(): int
    {
        return collect($this->findAll())
            ->map(fn (CloudInstalledWorkshop $worksop) => count($worksop->findAllExercises()))
            ->sum();
    }

    /**
     * The workshop code is the bin entry point in the workshop
     * so we need to decode
     */
    private function getWorkshopCode(string $path): string
    {
        $json = json_decode(file_get_contents($path . '/composer.json'), true);
        return basename($json['bin'][0]);
    }

    private function getComposerInstalledWorkshops(): array
    {
        return array_unique(InstalledVersions::getInstalledPackagesByType('php-school-workshop'));
    }
}
