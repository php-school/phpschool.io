<?php

namespace PhpSchool\Website\User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="student"))
 */
class Student implements \JsonSerializable
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     *
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $githubId;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255, unique=false)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=true)
     */
    private ?string $profilePicture;

    /**
     * @ORM\Column(type="string", length=255, unique=false, nullable=true)
     */
    private ?string $location;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $joinDate;


    public function __construct(
        string $githubId,
        string $username,
        string $email,
        string $name,
        ?string $profilePicture,
        ?string $location
    ) {
        $this->githubId = $githubId;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->profilePicture = $profilePicture;
        $this->location = $location;
        $this->joinDate = new DateTime();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getGithubId(): string
    {
        return $this->githubId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getJoinDate(): DateTime
    {
        return $this->joinDate;
    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->getEmail(),
            'username' => $this->getUsername(),
            'name' => $this->getName(),
            'profile_picture' => $this->getProfilePicture(),
            'location' => $this->getLocation(),
            'join_date' => $this->getJoinDate()->format('F Y')
        ];
    }
}
