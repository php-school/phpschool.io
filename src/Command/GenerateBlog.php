<?php

namespace PhpSchool\Website\Command;

use Exception;
use PhpSchool\Website\Blog\Generate;
use PhpSchool\Website\Blog\Generator;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class GenerateBlog
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Generate the API docs and save it to cache
     *
     * @param OutputInterface $output
     * @throws Exception
     */
    public function __invoke(OutputInterface $output)
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
