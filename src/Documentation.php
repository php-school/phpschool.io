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
     * @var DocumentationGroup[]
     */
    private $groups = [];

    /**
     * @param DocumentationGroup $group
     */
    public function addGroup(DocumentationGroup $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @param string $group
     * @param string $section
     * @return DocumentationSection
     */
    public function findSectionByGroupAndSection($group, $section) : DocumentationSection
    {
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

    public function hasPreviousSection(DocumentationSectionInterface $section) : bool
    {
        $group = $this->findGroupForSection($section);

        if (null === $group) {
            return false;
        }

        return $group->hasPreviousSection($section);
    }

    public function getPreviousSection(DocumentationSectionInterface $section) : DocumentationSectionInterface
    {
        return $this->findGroupForSection($section)->getPreviousSection($section);
    }

    public function hasNextSection(DocumentationSectionInterface $section)
    {
        $group = $this->findGroupForSection($section);

        if (null === $group) {
            return false;
        }

        return $group->hasNextSection($section);
    }

    public function getNextSection(DocumentationSectionInterface $section) : DocumentationSectionInterface
    {
        return $this->findGroupForSection($section)->getNextSection($section);
    }

    public function hasHome(DocumentationSectionInterface $section) : bool
    {
        if ($section->getName() === 'index') {
            return false;
        }

        $group = $this->findGroupForSection($section);

        if (null === $group) {
            return false;
        }

        return $group->getHome() !== null;
    }

    public function getHome(DocumentationSectionInterface $section) : DocumentationSectionInterface
    {
        return $this->findGroupForSection($section)->getHome();
    }

    /**
     * @param DocumentationSectionInterface $section
     * @return null|DocumentationGroup
     */
    private function findGroupForSection(DocumentationSectionInterface $section)
    {
        foreach ($this->groups as $group) {
            if ($group->hasSection($section)) {
                return $group;
            }
        }

        return null;
    }
}
