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

    public function addSection(string $name, string $title, string $template, bool $enabled = true)
    {
        $this->sections[] = new DocumentationSection(
            $name,
            $title,
            $template,
            sprintf('/docs/%s/%s', $this->name, $name),
            $enabled
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

    public function findSectionByName(string $name) : DocumentationSectionInterface
    {
        $doc = current(array_filter($this->sections, function (DocumentationSectionInterface $doc) use ($name) {
            return $doc->getName() === $name;
        }));

        if (false === $doc) {
            throw new \RuntimeException(sprintf('Section: "%s" does not exist', $name));
        }

        return $doc;
    }

    public function getHome()
    {
        return $this->sections[0];
    }

    public function hasSection(DocumentationSectionInterface $section) : bool
    {
        return in_array($section, $this->sections, true);
    }

    public function hasNextSection(DocumentationSectionInterface $section) : bool
    {
        $offset = $this->getSectionKey($section);
        return isset($this->sections[$offset + 1]);
    }

    public function getNextSection(DocumentationSectionInterface $section) : DocumentationSectionInterface
    {
        $offset = $this->getSectionKey($section);
        $offset += 1;

        if (!isset($this->sections[$offset])) {
            throw new \RuntimeException(sprintf('Section: "%s" has no next section', $section->getName()));
        }

        return $this->sections[$offset];
    }

    public function hasPreviousSection(DocumentationSectionInterface $section) : bool
    {
        $offset = $this->getSectionKey($section);
        return isset($this->sections[$offset - 1]);
    }

    public function getPreviousSection(DocumentationSectionInterface $section) : DocumentationSectionInterface
    {
        $offset = $this->getSectionKey($section);
        $offset -= 1;

        if (!isset($this->sections[$offset])) {
            throw new \RuntimeException(sprintf('Section: "%s" has no previous section', $section->getName()));
        }

        return $this->sections[$offset];
    }

    private function getSectionKey(DocumentationSectionInterface $section) : int
    {
        $offset = array_search($section, $this->sections, true);

        if (false === $offset) {
            throw new \RuntimeException(sprintf('Section: "%s" was not found', $section->getName()));
        }
        return $offset;
    }

    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->sections);
    }
}
