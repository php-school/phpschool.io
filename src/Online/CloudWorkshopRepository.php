<?php

declare(strict_types=1);

namespace PhpSchool\Website\Online;

use Composer\InstalledVersions;
use PhpSchool\Website\Repository\WorkshopRepository;
use RuntimeException;

class CloudWorkshopRepository
{
    public function __construct(
        private readonly WorkshopRepository $workshopRepository,
    ) {}

    /**
     * @param array<string, string> $vendorVolumes see DockerSandbox
     */
    public function findByCode(string $code, array $vendorVolumes = []): CloudInstalledWorkshop
    {
        foreach ($this->findAll($vendorVolumes) as $workshop) {
            if ($workshop->getCode() === $code) {
                return  $workshop;
            }
        }

        throw new RuntimeException(sprintf('Cannot find workshop with code: "%s"', $code));
    }

    /**
     * @param array<string, string> $vendorVolumes see DockerSandbox
     * @return array<CloudInstalledWorkshop>
     */
    public function findAll(array $vendorVolumes = []): array
    {
        $workshops = $this->getComposerInstalledWorkshops();

        return array_map(
            function (string $packageName) use ($vendorVolumes) {
                $path = (string) realpath((string) InstalledVersions::getInstallPath($packageName));

                $workshop = $this->workshopRepository->findByCode(
                    $this->getWorkshopCode($path),
                );

                return new CloudInstalledWorkshop(
                    include $path . '/app/bootstrap.php',
                    $workshop,
                    $path,
                    $vendorVolumes,
                );
            },
            $workshops,
        );
    }

    public function totalExerciseCount(): int
    {
        /** @var int $total */
        $total = collect($this->findAll())
            ->map(fn(CloudInstalledWorkshop $workshop) => count($workshop->findAllExercises()))
            ->sum();

        return $total;
    }

    /**
     * The workshop code is the bin entry point in the workshop
     * so we need to decode
     */
    private function getWorkshopCode(string $path): string
    {
        /** @var array{bin: non-empty-list<string>} $json */
        $json = json_decode((string) file_get_contents($path . '/composer.json'), true);
        return basename($json['bin'][0]);
    }

    /**
     * @return list<string>
     */
    private function getComposerInstalledWorkshops(): array
    {
        return array_values(array_unique(InstalledVersions::getInstalledPackagesByType('php-school-workshop')));
    }
}
