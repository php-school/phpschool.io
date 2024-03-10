<?php

declare(strict_types=1);

namespace PhpSchool\Website\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="workshop"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\Repository\DoctrineORMWorkshopRepository")
 */
class Workshop implements \JsonSerializable
{
    public const TYPE_COMMUNITY = 0;
    public const TYPE_CORE = 1;

    /**
     * @var array<int, 'core'|'community'>
     */
    private array $typeMap = [
        self::TYPE_COMMUNITY => 'community',
        self::TYPE_CORE => 'core',
    ];

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
     * @ORM\Column(type="string", length=255)
     */
    private string $gitHubOwner;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $gitHubRepoName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $code;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $displayName;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private string $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $submitterEmail;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $submitterName;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private ?string $submitterContact;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $approved;

    /**
     * @ORM\Column(type="integer")
     */
    private int $type = self::TYPE_COMMUNITY;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="WorkshopInstall", mappedBy="workshop")
     *
     * @var Collection<string, WorkshopInstall>
     */
    private Collection $installs;

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
        $this->createdAt = new DateTime();
        $this->installs = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getRepoUrl(): string
    {
        return sprintf('https://github.com/%s/%s', $this->getGitHubOwner(), $this->getGitHubRepoName());
    }

    public function getGitHubOwner(): string
    {
        return $this->gitHubOwner;
    }

    public function getGitHubRepoName(): string
    {
        return $this->gitHubRepoName;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSubmitterEmail(): string
    {
        return $this->submitterEmail;
    }

    public function getSubmitterName(): string
    {
        return $this->submitterName;
    }

    public function getSubmitterContact(): ?string
    {
        return $this->submitterContact;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function approve(): void
    {
        $this->approved = true;
    }

    public function isCommunity(): bool
    {
        return $this->type === self::TYPE_COMMUNITY;
    }

    public function isCore(): bool
    {
        return $this->type === self::TYPE_CORE;
    }

    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return 'core'|'community'
     */
    public function getTypeCode(): string
    {
        return $this->typeMap[$this->getType()];
    }

    /**
     * @return 'Core'|'Community'
     */
    public function getTypeName(): string
    {
        return ucfirst($this->getTypeCode());
    }

    public function promoteToCore(): void
    {
        $this->type = self::TYPE_CORE;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getTotalInstalls(): int
    {
        return $this->installs->count();
    }

    /**
     * @return array{
     *     workshop_code: string,
     *     display_name: string,
     *     github_owner: string,
     *     github_repo_name: string,
     *     repo_url: string,
     *     type: 'core'|'community',
     *     description: string
     * }
     */
    public function toArray(): array
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

    /**
     * @return array{
     *     id: string,
     *     code: string,
     *     name: string,
     *     description: string,
     *     repo_url: string,
     *     submitter_name: string,
     *     submitter_email: string,
     *     submitter_contact: string|null,
     *     submitter_avatar: string,
     *     status: 'Approved'|'Not-approved',
     *     type: 'Core'|'Community',
     *     installs: int,
     *     created_at: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'code' => $this->getCode(),
            'name' => $this->getDisplayName(),
            'description' => $this->getDescription(),
            'repo_url' => $this->getRepoUrl(),
            'submitter_name' => $this->getSubmitterName(),
            'submitter_email' => $this->getSubmitterEmail(),
            'submitter_contact' => $this->getSubmitterContact(),
            'submitter_avatar' => 'https://www.gravatar.com/avatar/' . md5($this->getSubmitterEmail()),
            'status' => $this->isApproved() ? 'Approved' : 'Not-approved',
            'type' => $this->getTypeName(),
            'installs' => $this->getTotalInstalls(),
            'created_at' => $this->getCreatedAt()->format('M jS, y h:i')
        ];
    }
}
