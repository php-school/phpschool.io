<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use PhpSchool\Website\Online\CloudWorkshopRepository;
use Symfony\Component\Console\Output\OutputInterface;

class BuildRuntimeImages
{
    public function __construct(private readonly CloudWorkshopRepository $workshopRepository) {}

    public function __invoke(OutputInterface $output): int
    {
        foreach ($this->workshopRepository->findAll() as $workshop) {
            $output->writeln(sprintf('<info>Building runtime image for %s</info>', $workshop->getCode()));

            $process = $workshop->createRuntimeImageBuildProcess();
            $process->run();

            if (!$process->isSuccessful()) {
                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
                return 1;
            }

            $output->writeln(sprintf('<info>Caching reference solution packages for %s</info>', $workshop->getCode()));
            $workshop->warmReferencePackages();
        }

        return 0;
    }
}
