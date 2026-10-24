<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use Exception;
use PhpSchool\Website\Blog\Generator;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateBlog
{
    public function __construct(
        private readonly Generator $generator,
    ) {}

    public function __invoke(OutputInterface $output): void
    {
        try {
            $this->generator->generate();
        } catch (Exception $e) {
            $output->writeln('<error>Blog generation failed</error>');
            throw $e;
        }

        $output->writeln('<info>Blog generated successfully</info>');
    }
}
