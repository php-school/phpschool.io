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
        $workshops = $this->workshopRepository->findAll();

        if (count($workshops) > 0) {
            $process = $workshops[0]->createSandboxNetworkProcess();
            $process->run();

            //it only needs creating once, every deploy after that it already exists
            if (!$process->isSuccessful() && !str_contains($process->getErrorOutput(), 'already exists')) {
                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
                return 1;
            }
        }

        foreach ($workshops as $workshop) {
            $output->writeln(sprintf('<info>Building runtime image for %s</info>', $workshop->getCode()));

            $process = $workshop->createRuntimeImageBuildProcess();
            $process->run();

            if (!$process->isSuccessful()) {
                $output->writeln(sprintf('<error>%s</error>', $process->getErrorOutput()));
                return 1;
            }
        }

        return 0;
    }
}
