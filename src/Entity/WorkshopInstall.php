<?php

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="workshop_installs"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMWorkshopInstallRepository")
 */
class WorkshopInstall
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id; /** @phpstan-ignore-line  */

    /**
     * @ORM\ManyToOne(targetEntity="Workshop", inversedBy="installs")
     * @ORM\JoinColumn(name="workshop_id", referencedColumnName="id")
     */
    private Workshop $workshop;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $dateTime;

    /**
     *
     * @ORM\Column(type="string", length=255)
     */
    private string $ipAddress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $version;

    public function __construct(Workshop $workshop, string $ipAddress, string $version)
    {
        $this->workshop = $workshop;
        $this->dateTime = new \DateTime();
        $this->ipAddress = $ipAddress;
        $this->version = $version;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getWorkshop(): Workshop
    {
        return $this->workshop;
    }

    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}
