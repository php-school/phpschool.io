<?php

declare(strict_types=1);

namespace PhpSchool\Website\Blog;

use DateTime;
use JsonSerializable;

class PostMeta implements JsonSerializable
{
    private string $title;
    private DateTime $date;
    private string $link;
    private string $author;
    private string $authorLink;

    public function __construct(string $title, DateTime $date, string $author, string $authorLink)
    {
        $this->title = $title;
        $this->date = $date;
        $this->author = $author;
        $this->authorLink = $authorLink;
        $this->link = $this->slugify($this->title);
    }

    /**
     * @param array{title: string, date: string, author: string, author_link: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self($data['title'], new DateTime('@' . $data['date']), $data['author'], $data['author_link']);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthorLink(): string
    {
        return $this->authorLink;
    }

    private function slugify(string $string): string
    {
        return trim(strtolower((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $string)), '-');
    }

    /**
     * @return array{
     *     title: string,
     *     author: string,
     *     authorLink: string,
     *     date: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'title' => $this->getTitle(),
            'author' => $this->getAuthor(),
            'authorLink' => $this->getAuthorLink(),
            'date' => $this->date->format('F j, Y'),
        ];
    }
}
