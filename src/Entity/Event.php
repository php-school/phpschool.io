<?php

declare(strict_types=1);

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="event"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMEventRepository")
 */
class Event implements \JsonSerializable
{
    /**
     *
     * @psalm-suppress PropertyNotSetInConstructor
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id; /** @phpstan-ignore-line  */

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private ?string $link;

    /**
     * @ORM\Column(type="datetime", length=512)
     */
    private DateTime $dateTime;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private string $venue;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private ?string $poster;

    public function __construct(
        string $name,
        string $description,
        ?string $link,
        DateTime $dateTime,
        string $venue,
        ?string $poster
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->link = $link;
        $this->dateTime = $dateTime;
        $this->venue = $venue;
        $this->poster = $poster;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Event
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Event
    {
        $this->description = $description;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link = null): Event
    {
        $this->link = $link;
        return $this;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(DateTime $dateTime): Event
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    public function getVenue(): string
    {
        return $this->venue;
    }

    public function setVenue(string $venue): Event
    {
        $this->venue = $venue;
        return $this;
    }

    /**
     * @return array<string>
     */
    public function getVenueLines(): array
    {
        return explode("\n", $this->venue);
    }

    public function getPoster(): ?string
    {
        return $this->poster;
    }

    public function setPoster(?string $poster): Event
    {
        $this->poster = $poster;
        return $this;
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     link: ?string,
     *     date_formatted: string,
     *     date: string,
     *     venue: string,
     *     venueLines: array<string>,
     *     poster: ?string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'link' => $this->getLink(),
            'date_formatted' => $this->getDateTime()->format('l M jS - g:i A'),
            'date' => $this->getDateTime()->format('Y-m-d\TH:i'),
            'venue' => $this->getVenue(),
            'venueLines' => $this->getVenueLines(),
            'poster' => $this->getPoster(),
        ];
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     link: ?string,
     *     date_formatted: string,
     *     date: string,
     *     venue: string,
     *     venueLines: array<string>,
     *     poster: ?string
     * }
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
