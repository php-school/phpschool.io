<?php

namespace PhpSchool\Website;

/**
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
interface DocumentationSectionInterface
{
    public function getName() : string;

    public function getTitle() : string;

    public function getHref() : string;

    public function enabled() : bool;
}
