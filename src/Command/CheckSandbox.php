<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use PhpSchool\PhpWorkshop\Exercise\ProvidesSolution;
use PhpSchool\PhpWorkshop\Input\Input;
use PhpSchool\PhpWorkshop\Result\FailureInterface;
use PhpSchool\PhpWorkshop\Utils\Path;
use PhpSchool\PhpWorkshop\Utils\System;
use PhpSchool\Website\Online\CloudWorkshopRepository;
use PhpSchool\Website\Online\Sandbox;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Verifies Hello World with its own solution, the way a student's verify runs, so an error is logged (and posted to
 * slack) before a student finds the sandbox broken. Run it hourly from cron: each check wakes the suspended Fly
 * machine, see fly/README.md
 */
class CheckSandbox
{
    public function __construct(
        private readonly CloudWorkshopRepository $workshops,
        private readonly LoggerInterface $logger,
        private readonly Sandbox $sandbox,
    ) {}

    public function __invoke(OutputInterface $output): int
    {
        try {
            $workshop = $this->workshops->findByCode('learnyouphp');
            $exercise = $workshop->findExerciseByName('Hello World');

            if (!$exercise instanceof ProvidesSolution) {
                throw new \RuntimeException('Hello World has no solution');
            }

            //a copy, as the verify patches the student's files in place, and this solution is the reference one too
            $copy = System::randomTempDir();
            (new Filesystem())->mirror($exercise->getSolution()->getBaseDirectory(), $copy);

            try {
                $results = $this->sandbox->run(fn() => $workshop->getExerciseDispatcher()->verify(
                    $exercise,
                    new Input($workshop->getCode(), ['program' => Path::join($copy, $exercise->getSolution()->getEntryPoint()->getRelativePath())]),
                ));
            } finally {
                (new Filesystem())->remove($copy);
            }
        } catch (\Throwable $e) {
            $this->logger->error('The sandbox could not verify Hello World. Error: ' . $e->getMessage());
            return 1;
        }

        if (!$results->isSuccessful()) {
            //eg. ["Program Output Check: Output did not match"]
            $failures = array_map(
                fn(FailureInterface $failure) => $failure->getCheckName() . ': ' . json_encode($failure->toArray()),
                array_filter(iterator_to_array($results), fn($result) => $result instanceof FailureInterface),
            );
            $this->logger->error('The sandbox could not verify Hello World. Failures: ' . implode(', ', $failures));
            return 1;
        }

        $output->writeln('<info>The sandbox verified Hello World</info>');

        return 0;
    }
}
