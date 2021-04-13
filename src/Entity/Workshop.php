<?php

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="workshop"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMWorkshopRepository")
 */
class Workshop
{
    const TYPE_COMMUNITY = 0;
    const TYPE_CORE = 1;

    /**
     * @var array
     */
    private $typeMap = [
        self::TYPE_COMMUNITY => 'community',
        self::TYPE_CORE => 'core',
    ];

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
    private $gitHubOwner;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $gitHubRepoName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $code;

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
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $submitterEmail;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $submitterName;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $submitterContact;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $approved;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $type = self::TYPE_COMMUNITY;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="WorkshopInstall", mappedBy="workshop")
     */
    private $installs;

    public function __construct(
        string $gitHubOwner,
        string $gitHubRepoName,
        string $name,
        string $displayName,
        string $description,
        string $submitterEmail,
        string $submitterName,
        string $submitterContact = null,
        bool $approved = false
    ) {
        $this->gitHubOwner = $gitHubOwner;
        $this->gitHubRepoName = $gitHubRepoName;
        $this->code = $name;
        $this->displayName = $displayName;
        $this->description = $description;
        $this->submitterEmail = $submitterEmail;
        $this->submitterName = $submitterName;
        $this->submitterContact = $submitterContact;
        $this->approved = $approved;
        $this->createdAt = new DateTime;
        $this->installs = new ArrayCollection;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRepoUrl(): string
    {
        return sprintf('https://github.com/%s/%s', $this->getGitHubOwner(), $this->getGitHubRepoName());
    }

    public function getGitHubOwner() : string
    {
        return $this->gitHubOwner;
    }

    public function getGitHubRepoName() : string
    {
        return $this->gitHubRepoName;
    }

    public function getCode() : string
    {
        return $this->code;
    }

    public function getDisplayName() : string
    {
        return $this->displayName;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getSubmitterEmail() : string
    {
        return $this->submitterEmail;
    }

    public function getSubmitterName() : string
    {
        return $this->submitterName;
    }

    /**
     * @return string|null
     */
    public function getSubmitterContact()
    {
        return $this->submitterContact;
    }

    public function isApproved() : bool
    {
        return $this->approved;
    }

    public function approve()
    {
        $this->approved = true;
    }

    public function isCommunity() : bool
    {
        return $this->type === self::TYPE_COMMUNITY;
    }

    public function isCore() : bool
    {
        return $this->type === self::TYPE_CORE;
    }

    public function getType() : int
    {
        return $this->type;
    }

    public function getTypeCode() : string
    {
        return $this->typeMap[$this->getType()];
    }

    public function getTypeName() : string
    {
        return ucfirst($this->getTypeCode());
    }

    public function promoteToCore()
    {
        $this->type = self::TYPE_CORE;
    }

    public function getCreatedAt() : DateTime
    {
        return $this->createdAt;
    }

    public function getTotalInstalls() : int
    {
        return $this->installs->count();
    }

    public function toArray() : array
    {
        return [
            "workshop_code" => $this->getCode(),
            "display_name" => $this->getDisplayName(),
            "github_owner" => $this->getGitHubOwner(),
            "github_repo_name" => $this->getGitHubRepoName(),
            'repo_url' => $this->getRepoUrl(),
            'type' => $this->getTypeCode(),
            "description" => $this->getDescription()
        ];
    }
}
