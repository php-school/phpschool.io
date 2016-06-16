<?php

namespace PhpSchool\Website;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class DocumentationGroup
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocumentationGroup implements IteratorAggregate
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
     * @var DocumentationSectionInterface[]
     */
    private $sections = [];

    public function __construct(string $name, string $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function addSection(string $name, string $title, string $template)
    {
        $this->sections[] = new DocumentationSection(
            $name,
            $title,
            $template,
            sprintf('/docs/%s/%s', $this->name, $name)
        );
    }

    public function addExternalSection(string $name, string $title, string $href, bool $enabled = true)
    {
        $this->sections[] = new ExternalDocumentationSection($name, $title, $href, $enabled);
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function hasSection(DocumentationSectionInterface $section) : bool
    {
        return in_array($section, $this->sections, true);
    }

    public function getSectionOffset(DocumentationSectionInterface $section) : int
    {
        return array_search($section, $this->sections);
    }

    public function sectionExists(int $offset) : bool
    {
        return isset($this->sections[$offset]);
    }

    public function getSectionAtOffset(int $offset) : DocumentationSectionInterface
    {
        return $this->sections[$offset];
    }

    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->sections);
    }

    public function getSections() : array
    {
        return $this->sections;
    }
}
