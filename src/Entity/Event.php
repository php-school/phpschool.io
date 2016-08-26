<?php

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="event"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMEventRepository")
 */
class Event
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", length=512)
     */
    private $dateTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $venue;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $poster;

    public function __construct(string $name, string $description, DateTime $dateTime, string $venue, string $poster = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->dateTime = $dateTime;
        $this->venue = $venue;
        $this->poster = $poster;
    }

    public function getId() : Uuid
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getVenue(): string
    {
        return $this->venue;
    }

    public function getVenueLines(): array
    {
        return explode("\n", $this->venue);
    }

    /**
     * @return null|string
     */
    public function getPoster()
    {
        return $this->poster;
    }
}
