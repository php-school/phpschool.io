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
     * @var DocumentationSection|null
     */
    private $index;

    /**
     * @var DocumentationSection
     */
    private $sections = [];

    public function __construct(string $name, string $title)
    {
        $this->name = $name;
        $this->title = $title;
    }

    public function setIndex(string $title, string $template)
    {
        $this->index = new DocumentationSection('index', $title, $template, sprintf('/docs/%s/index', $this->name));
    }

    /**
     * @param string $name
     * @param string $title
     * @param string $template
     */
    public function addSection(string $name, string $title, string $template)
    {
        $doc        = new DocumentationSection($name, $title, $template, sprintf('/docs/%s/%s', $this->name, $name));
        $sections   = $this->sections;
        $prev       = end($sections);

        if ($prev instanceof DocumentationSection) {
            $prev->setNext($doc);
            $doc->setPrev($prev);
        }

        if (null !== $this->index) {
            $doc->setHome($this->index);
        }

        $this->sections[] = $doc;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @param string $name
     * @return DocumentationSection
     */
    public function findSectionByName(string $name) : DocumentationSection
    {
        if ($name === 'index' && null !== $this->index) {
            return $this->index;
        }

        $doc = current(array_filter($this->sections, function (DocumentationSection $doc) use ($name) {
            return $doc->getName() === $name;
        }));

        if (false === $doc) {
            throw new \RuntimeException(sprintf('Section: "%s" does not exist', $name));
        }

        return $doc;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->sections);
    }
}
