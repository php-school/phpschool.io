<?php

declare(strict_types=1);

namespace PhpSchool\Website\Command;

use Exception;
use PhpSchool\Website\Blog\Generator;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateBlog
{
    private Generator $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

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
