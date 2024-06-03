<?php

declare(strict_types=1);

namespace PhpSchool\Website\Blog;

class Post
{
    private PostMeta $meta;
    private string $content;

    public function __construct(PostMeta $meta, string $content)
    {
        $this->meta = $meta;
        $this->content = $content;

        $this->content = (string) preg_replace_callback(
            '/<pre><code class="language-shell">(.*?)<\/code><\/pre>/s',
            function ($matches) {
                return sprintf(
                    "<doc-terminal :lines='%s'></doc-terminal>",
                    json_encode(explode("\n", $matches[1]), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR),
                );
            },
            $this->content
        );
    }

    public function getMeta(): PostMeta
    {
        return $this->meta;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFeatureImage(): string
    {
        $content = $this->getContent();

        preg_match('/<img\s[^>]*?src\s*=\s*[\'\"]([^\'\"]*?)[\'\"][^>]*?>/', $content, $matches);

        return $matches[0];
    }
}
