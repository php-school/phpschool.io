<?php

namespace PhpSchool\Website\Blog;

use GlobIterator;
use Mni\FrontYAML\Document;
use Mni\FrontYAML\Parser;
use PhpSchool\Website\Entity\BlogPost;
use PhpSchool\Website\Repository\DoctrineORMBlogRepository;

class Generator
{
    public function __construct(
        private readonly Parser $parser,
        private readonly DoctrineORMBlogRepository $repository,
        private string $postsDirectory,
    ) {
        $this->postsDirectory = rtrim($postsDirectory, '/');
    }

    public function generate(): void
    {
        $this->deletePosts();

        collect($this->getMarkDownFiles())
            ->map(function (\SplFileInfo $file) {
                return $this->parser->parse((string) file_get_contents($file->getRealPath()));
            })
            ->each(function (Document $document) {
                $meta = $document->getYAML();
                if (!is_array($meta)) {
                    throw new \RuntimeException('Post meta invalid');
                }

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
                return $documentB->getYAML()['date'] <=> $documentA->getYAML()['date']; //@phpstan-ignore-line
            })
            ->map(function (Document $document) {
                return new Post(PostMeta::fromArray($document->getYAML()), $document->getContent()); //@phpstan-ignore-line
            })
            ->each(function (Post $post) {
                $this->repository->save(BlogPost::fromPost($post));
            });
    }

    private function getMarkdownFiles(): GlobIterator
    {
        return new GlobIterator(sprintf('%s/*.md', rtrim($this->postsDirectory, '/')));
    }

    private function deletePosts(): void
    {
        $this->repository->deleteAll();
    }
}
