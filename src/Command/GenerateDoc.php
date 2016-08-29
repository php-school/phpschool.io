<?php

namespace PhpSchool\Website\Command;

use Exception;
use PhpSchool\Website\DocGenerator;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class GenerateDoc
{
    /**
     * @var DocGenerator
     */
    private $docGenerator;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    /**
     * @param DocGenerator $docGenerator
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(DocGenerator $docGenerator, CacheItemPoolInterface $cache)
    {
        $this->docGenerator = $docGenerator;
        $this->cache = $cache;
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
            $docs = $this->docGenerator->generate();
        } catch (Exception $e) {
            $output->writeln('<error>Docs generation failed</error>');
            throw $e;
        }
        $apiDocsCache = $this->cache->getItem('api-docs');
        $apiDocsCache->set($docs);
        $this->cache->save($apiDocsCache);

        $output->writeln('<info>Docs generated successfully</info>');
    }
}
