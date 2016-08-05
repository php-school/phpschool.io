<?php

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="workshop_installs"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMWorkshopInstallRepository")
 */
class WorkshopInstall
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
     * @var Workshop
     *
     * @ORM\ManyToOne(targetEntity="Workshop")
     * @ORM\JoinColumn(name="workshop_id", referencedColumnName="id")
     */
    private $workshop;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $version;

    public function __construct(Workshop $workshop, string $ipAddress, string $version)
    {
        $this->workshop = $workshop;
        $this->dateTime = new \DateTime;
        $this->ipAddress = $ipAddress;
        $this->version = $version;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWorkshop() : Workshop
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
