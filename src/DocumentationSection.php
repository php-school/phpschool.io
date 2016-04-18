<?php

namespace PhpSchool\Website;

/**
 * Class DocumentationSection
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocumentationSection
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
    private $templateFile;

    /**
     * @var self|null
     */
    private $prev;

    /**
     * @var self|null
     */
    private $next;

    /**
     * @var self|null
     */
    private $home;

    /**
     * @var string
     */
    private $href;

    /**
     * DocumentationSection constructor.
     * @param string $name
     * @param string $title
     * @param string $templateFile
     * @param string $href
     */
    public function __construct(
        string $name,
        string $title,
        string $templateFile,
        string $href
    ) {
        $this->name = $name;
        $this->title = $title;
        $this->templateFile = $templateFile;
        $this->href = $href;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTitle() : string
    {
        return $this->title;
    }

    public function getTemplateFile() : string
    {
        return $this->templateFile;
    }

    /**
     * @param self|null $prev
     */
    public function setPrev($prev)
    {
        $this->prev = $prev;
    }

    /**
     * @return self|null
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * @param self|null $next
     */
    public function setNext($next)
    {
        $this->next = $next;
    }

    /**
     * @return self|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param self|null $home
     */
    public function setHome($home)
    {
        $this->home = $home;
    }

    /**
     * @return self|null
     */
    public function getHome()
    {
        return $this->home;
    }

    public function getHref() : string
    {
        return $this->href;
    }
}
