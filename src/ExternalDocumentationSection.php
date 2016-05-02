<?php

namespace PhpSchool\Website;

/**
 * Class ExternalDocumentationSection
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ExternalDocumentationSection implements DocumentationSectionInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $href;

    /**
     * @var bool
     */
    private $enabled;

    public function __construct(
        string $name,
        string $title,
        string $href,
        bool $enabled = true
    ) {

        $this->name = $name;
        $this->title = $title;
        $this->href = $href;
        $this->enabled = $enabled;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getHref() : string
    {
        return $this->href;
    }

    public function enabled() : bool
    {
        return $this->enabled;
    }
}
