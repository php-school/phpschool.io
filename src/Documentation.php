<?php

namespace PhpSchool\Website;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class Documentation
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Documentation implements IteratorAggregate
{
    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var null|DocumentationSection
     */
    private $index;

    public function setIndex(string $title, string $template)
    {
        $this->index = new DocumentationSection('index', $title, $template, sprintf('/docs'));
    }

    /**
     * @param DocumentationGroup $group
     */
    public function addGroup(DocumentationGroup $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @param string $group
     * @param string $name
     * @param string $title
     * @param string $template
     */
    public function addSectionToGroup(string $group, string $name, string $title, string $template)
    {
        if (!isset($this->groups[$group])) {
            throw new \InvalidArgumentException(sprintf('Group: "%s" does not exist', $group));
        }

        $doc        = new DocumentationSection($name, $title, $template);
        $sections   = $this->groups[$group];
        $prev       = end($sections);

        if ($prev instanceof DocumentationSection) {
            $prev->setNext($doc);
            $doc->setPrev($prev);
        }

        $this->groups[$group][] = $doc;
    }

    /**
     * @param string $group
     * @param string $section
     * @return DocumentationSection
     */
    public function findSectionByGroupAndSection($group, $section) : DocumentationSection
    {
        if ($group === 'index' && $section === 'index' && null !== $this->index) {
            return $this->index;
        }

        return $this->findGroupByName($group)->findSectionByName($section);
    }

    /**
     * @param string $name
     * @return DocumentationGroup
     */
    public function findGroupByName(string $name) : DocumentationGroup
    {
        $group = current(array_filter($this->groups, function (DocumentationGroup $group) use ($name) {
            return $group->getName() === $name;
        }));

        if (false === $group) {
            throw new \RuntimeException(sprintf('Group: "%s" does not exist', $name));
        }

        return $group;
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->groups);
    }
}
