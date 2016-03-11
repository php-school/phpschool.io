<?php

namespace PhpSchool\Website\Command;

use PhpSchool\Website\DocGenerator;


/**
 * Class GenerateDoc
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class GenerateDoc
{
    /**
     * @var string
     */
    private $apiCacheFile = __DIR__ . '/../../cache/api-docs.json';

    /**
     * @param DocGenerator $docGenerator
     */
    public function __invoke(DocGenerator $docGenerator)
    {
        $docs = $docGenerator->generate();
        file_put_contents($this->apiCacheFile, json_encode($docs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
