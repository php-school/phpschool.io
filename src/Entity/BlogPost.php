<?php

declare(strict_types=1);

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PhpSchool\Website\Blog\Post;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMBlogRepository")
 */
class BlogPost implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    public UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $slug;

    /**
     * @ORM\Column(type="text", length=65535, options={"charset"="utf8mb4"})
     */
    public string $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $featuredImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $title;

    /**
     * @ORM\Column(type="datetime", length=512)
     */
    public DateTime $dateTime;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $authorLink;

    public static function fromPost(Post $post): self
    {
        $self = new self();
        $self->slug = $post->getMeta()->getLink();
        $self->content = $post->getContent();
        $self->featuredImage = $post->getFeatureImage();
        $self->title = $post->getMeta()->getTitle();
        $self->dateTime = $post->getMeta()->getDate();
        $self->author = $post->getMeta()->getAuthor();
        $self->authorLink = $post->getMeta()->getAuthorLink();

        return $self;
    }

    public function getExcerpt(): string
    {
        $content = strip_tags($this->content);
        return substr($content, 0, 200) . "...";
    }

    /**
     * @return array{
     *     content: string,
     *     featuredImage: string,
     *     excerpt: string,
     *     title: string,
     *     slug: string,
     *     author: string,
     *     authorLink: string,
     *     date: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'content' => $this->content,
            'featuredImage' => $this->featuredImage,
            'excerpt' => $this->getExcerpt(),
            'title' => $this->title,
            'slug' => $this->slug,
            'author' => $this->author,
            'authorLink' => $this->authorLink,
            'date' => $this->dateTime->format('F j, Y'),
        ];
    }
}
