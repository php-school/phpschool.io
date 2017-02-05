<?php

namespace PhpSchool\Website\Blog;

use DateTime;
use GlobIterator;
use Illuminate\Support\Collection;
use Mni\FrontYAML\Document;
use Mni\FrontYAML\Parser;
use PhpSchool\Website\PhpRenderer;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Generator
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var string
     */
    private $postsDirectory;

    /**
     * @var string
     */
    private $outputDirectory;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(Parser $parser, string $postsDirectory, string $outputDirectory, PhpRenderer $renderer)
    {
        $this->parser = $parser;
        $this->postsDirectory = rtrim($postsDirectory, '/');
        $this->outputDirectory = rtrim($outputDirectory, '/');
        $this->renderer = $renderer;

        $this->renderer->addAttribute('route', '/blog');
    }

    public function generate()
    {
        $this->clearOutputDirectory();

        $posts = collect($this->getMarkDownFiles())
            ->ifEmpty(function () {
                //generate blank index
                $content = $this->rendererPostIndexPage(collect(), 1, 1);
                $this->write(sprintf('%s/index.html', $this->outputDirectory), $content);
            })
            ->map(function (\SplFileInfo $file) {
                return $this->parser->parse(file_get_contents($file->getRealPath()));
            })
            ->each(function (Document $document) {
                $meta = $document->getYAML();
                $missing = array_diff_key(array_flip(['date', 'title', 'author', 'author_link']), $meta);

                if (count($missing) > 0) {
                    throw new \RuntimeException(
                        sprintf(
                            'Post meta keys: "%s" %s missing and required',
                            implode(", ", array_keys($missing)),
                            count($missing) > 1 ? 'are' : 'is'
                        )
                    );
                }
            })
            ->sort(function (Document $documentA, Document $documentB) {
                return $documentB->getYAML()['date'] <=> $documentA->getYAML()['date'];
            })
            ->map(function (Document $document) {
                return new Post(PostMeta::fromArray($document->getYAML()), $document->getContent());
            });

        $posts
            ->each(function (Post $post) {
                $path = $this->getPostPath($post->getMeta()) . '.html';
                $this->write($path, $this->renderInLayout($post));
            });

        $numPages = ceil($posts->count() / 10);

        $posts
            ->chunk(10)
            ->each(function (Collection $posts, $i) use ($numPages) {
                $pageNumber = $i + 1;

                $fileName = sprintf(
                    '%s/%s.html',
                    $this->outputDirectory,
                    sprintf('page-%s', $pageNumber)
                );

                $content = $this->rendererPostIndexPage($posts, $pageNumber, $numPages);
                $this->write($fileName, $content);

                if ($pageNumber === 1) {
                    $this->write(sprintf('%s/index.html', $this->outputDirectory), $content);
                }
            });
    }

    private function getMarkdownFiles()
    {
        return new GlobIterator(sprintf('%s/*.md', rtrim($this->postsDirectory, '/')));
    }

    private function getPostPath(PostMeta $postMeta)
    {
        return sprintf(
            '%s%s',
            $this->outputDirectory,
            $postMeta->getLink()
        );
    }

    private function renderInLayout(Post $post) : string
    {
        return $this->renderer->fetch('layouts/layout.phtml', [
            'pageTitle'       => 'Blog',
            'pageDescription' => 'PHP School blog',
            'content'         => $this->renderer->fetch(
                'blog.phtml',
                [
                    'content' => $post->getContent(),
                    'meta'    => $post->getMeta()
                ]
            )
        ]);
    }

    private function rendererPostIndexPage(Collection $posts, int $pageNumber, int $numPages) : string
    {
        return $this->renderer->fetch('layouts/layout.phtml', [
            'pageTitle'       => 'PHP School Blog - Page ' . $pageNumber,
            'pageDescription' => 'PHP School Blog - Page ' . $pageNumber,
            'content'         => $this->renderer->fetch(
                'blog-index.phtml',
                [
                    'posts'         => $posts,
                    'pageNumber'    => $pageNumber,
                    'totalPages'    => $numPages
                ]
            )
        ]);
    }

    private function clearOutputDirectory()
    {
        if (!file_exists($this->outputDirectory)) {
            return;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->outputDirectory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            $todo = $file->isDir() ? 'rmdir' : 'unlink';
            $todo($file->getRealPath());
        }
    }

    private function write(string $file, string $content)
    {
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }

        file_put_contents($file, $content);
    }
}
