<?php

namespace PhpSchool\Website\Blog;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Post
{
    /**
     * @var PostMeta
     */
    private $meta;

    /**
     * @var string
     */
    private $content;

    public function __construct(PostMeta $meta, string $content)
    {
        $this->meta = $meta;
        $this->content = $content;
    }

    /**
     * @return PostMeta
     */
    public function getMeta(): PostMeta
    {
        return $this->meta;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    public function hasFeatureImage(): bool
    {
        $content = $this->getContent();

        return preg_match('/<img\s[^>]*?src\s*=\s*[\'\"]([^\'\"]*?)[\'\"][^>]*?>/', $content, $matches);
    }

    public function getFeatureImage(): string
    {
        $content = $this->getContent();

        preg_match('/<img\s[^>]*?src\s*=\s*[\'\"]([^\'\"]*?)[\'\"][^>]*?>/', $content, $matches);

        return $matches[0];
    }

    public function getExcerpt(): string
    {
        $content = $this->getContent();
        $content = strip_tags($content);
        return substr($content, 0, 200) . "...";
    }
}
