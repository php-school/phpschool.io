<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Checks the docker host student code runs on answers, so an error is logged (and posted to slack) before a student
 * finds out. Run it hourly from cron: each check wakes the suspended Fly machine, see fly/README.md
 */
class CheckSandbox
{
    public function __construct(private readonly LoggerInterface $logger) {}

    public function __invoke(OutputInterface $output): int
    {
        //waking the machine takes a few seconds
        $process = new Process(['docker', 'version', '--format', '{{.Server.Version}}'], timeout: 60);

        try {
            $process->run();
        } catch (\RuntimeException $e) {
            $this->logger->error('The sandbox docker host is not reachable. Error: ' . $e->getMessage());
            return 1;
        }

        if (!$process->isSuccessful()) {
            $this->logger->error('The sandbox docker host is not reachable. Error: ' . trim($process->getErrorOutput()));
            return 1;
        }

        $output->writeln(sprintf('<info>The sandbox docker host is up, docker %s</info>', trim($process->getOutput())));

        return 0;
    }
}
