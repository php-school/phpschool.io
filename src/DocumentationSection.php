<?php

namespace PhpSchool\Website;

/**
 * Class DocumentationSection
 * @package PhpSchool\Website
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class DocumentationSection implements DocumentationSectionInterface
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
     * @var string
     */
    private $href;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTemplateFile(): string
    {
        return $this->templateFile;
    }

    public function getHref(): string
    {
        //strip off trainling `/index`
        return preg_replace('/(\/index)+$/', '', $this->href);
    }
}
