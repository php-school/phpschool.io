<?php

namespace PhpSchool\Website\Blog;

class Post
{
    private PostMeta $meta;
    private string $content;

    public function __construct(PostMeta $meta, string $content)
    {
        $this->meta = $meta;
        $this->content = $content;
    }

    public function getMeta(): PostMeta
    {
        return $this->meta;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function hasFeatureImage(): bool
    {
        $content = $this->getContent();

        return (bool) preg_match('/<img\s[^>]*?src\s*=\s*[\'\"]([^\'\"]*?)[\'\"][^>]*?>/', $content, $matches);
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
