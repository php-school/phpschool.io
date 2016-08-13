<?php

namespace PhpSchool\Website\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Ramsey\Uuid\Uuid;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="workshop"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMWorkshopRepository")
 */
class Workshop
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
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=512)
     */
    private $description;

    /**
     * @var bool
     *
     *
     * @ORM\Column(type="boolean")
     */
    private $approved;

    public function __construct(
        string $owner,
        string $user,
        string $name,
        string $displayName,
        string $description,
        bool $approved = false
    ) {
        $this->owner = $owner;
        $this->user = $user;
        $this->name = $name;
        $this->displayName = $displayName;
        $this->description = $description;
        $this->approved = $approved;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getRepoUrl(): string
    {
        return sprintf('https://github.com/%s/%s', $this->getOwner(), $this->getUser());
    }

    public function getOwner(): string
    {
        return $this->owner;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isApproved() : bool
    {
        return $this->approved;
    }

    public function approve()
    {
        $this->approved = true;
    }

    public function toArray() : array
    {
        return [
            "name" => $this->getName(),
            "display_name" => $this->getDisplayName(),
            "owner" => $this->getOwner(),
            "repo" => $this->getUser(),
            'repo_url' => $this->getRepoUrl(),
            "description" => $this->getDescription()
        ];
    }
}
