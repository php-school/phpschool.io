<?php

namespace PhpSchool\Website;

use ArrayIterator;
use IteratorAggregate;

class Documentation implements IteratorAggregate
{
    /**
     * @var list<DocumentationGroup>
     */
    private array $groups = [];

    public function addGroup(DocumentationGroup $group): void
    {
        $this->groups[] = $group;
    }

    public function findSectionByGroupAndSection(string $group, string $sectionName): DocumentationSection
    {
        $group      = $this->findGroupByName($group);
        $sections   = $group->getSections();

        $doc = current(array_filter($sections, function (DocumentationSectionInterface $doc) use ($sectionName) {
            return $doc->getName() === $sectionName;
        }));

        if (false === $doc) {
            throw new \RuntimeException(sprintf('Section: "%s" does not exist', $sectionName));
        }

        return $doc;
    }

    public function findGroupByName(string $name): DocumentationGroup
    {
        $group = current(array_filter($this->groups, function (DocumentationGroup $group) use ($name) {
            return $group->getName() === $name;
        }));

        if (false === $group) {
            throw new \RuntimeException(sprintf('Group: "%s" does not exist', $name));
        }

        return $group;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->groups);
    }

    public function hasPreviousSection(DocumentationSectionInterface $section): bool
    {
        $group = $this->findGroupForSection($section);
        return $group->sectionExists($group->getSectionOffset($section) - 1);
    }

    public function getPreviousSection(DocumentationSectionInterface $section): DocumentationSectionInterface
    {
        $group      = $this->findGroupForSection($section);
        $offset     = $group->getSectionOffset($section) - 1;

        if (!$group->sectionExists($offset)) {
            throw new \RuntimeException(sprintf('Section: "%s" has no previous section', $section->getName()));
        }

        return $group->getSectionAtOffset($offset);
    }

    public function hasNextSection(DocumentationSectionInterface $section): bool
    {
        $group = $this->findGroupForSection($section);
        return $group->sectionExists($group->getSectionOffset($section) + 1);
    }

    public function getNextSection(DocumentationSectionInterface $section): DocumentationSectionInterface
    {
        $group      = $this->findGroupForSection($section);
        $offset     = $group->getSectionOffset($section) + 1;

        if (!$group->sectionExists($offset)) {
            throw new \RuntimeException(sprintf('Section: "%s" has no next section', $section->getName()));
        }

        return $group->getSectionAtOffset($offset);
    }

    public function hasHome(DocumentationSectionInterface $section): bool
    {
        if ($section->getName() === 'index') {
            return false;
        }

        return $this->findGroupForSection($section)->sectionExists(0);
    }

    public function getHome(DocumentationSectionInterface $section): DocumentationSectionInterface
    {
        return $this->findGroupForSection($section)->getSectionAtOffset(0);
    }

    private function findGroupForSection(DocumentationSectionInterface $section): DocumentationGroup
    {
        foreach ($this->groups as $group) {
            if ($group->hasSection($section)) {
                return $group;
            }
        }

        throw new \RuntimeException(sprintf('Section: "%s" belongs to no group', $section->getName()));
    }
}
