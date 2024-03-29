<?php

declare(strict_types=1);

namespace PhpSchool\Website\User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use PhpSchool\PhpWorkshop\UserState\UserState;
use PhpSchool\Website\Online\StudentCloudState;
use PhpSchool\Website\User\StudentDTO;
use Ramsey\Uuid\UuidInterface;

/**
 * @phpstan-import-type WorkshopState from \PhpSchool\Website\Online\StudentCloudState
 *
 * @ORM\Entity
 * @ORM\Table(name="student"))
 * @ORM\Entity(repositoryClass="PhpSchool\Website\User\DoctrineORMStudentRepository")
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
    private UuidInterface $id; /** @phpstan-ignore-line */

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $githubId;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $username;

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

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $tourComplete = false;

    /**
     * @var WorkshopState
     *
     * @ORM\Column(type="json", nullable=false)
     */
    private array $workshopState = [];

    /**
     * @param WorkshopState $workshopState
     */
    public function __construct(
        string $githubId,
        string $username,
        string $email,
        string $name,
        ?string $profilePicture,
        ?string $location,
        array $workshopState = [],
        bool $tourComplete = false,
    ) {
        $this->githubId = $githubId;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->profilePicture = $profilePicture;
        $this->location = $location;
        $this->joinDate = new DateTime();
        $this->workshopState = $workshopState;
        $this->tourComplete = $tourComplete;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getGithubId(): string
    {
        return $this->githubId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
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

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setProfilePicture(?string $profilePicture): void
    {
        $this->profilePicture = $profilePicture;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return WorkshopState
     */
    public function getWorkshopState(): array
    {
        return $this->workshopState;
    }

    /**
     * @param WorkshopState $state
     */
    public function setWorkshopState(array $state): void
    {
        $this->workshopState = $state;
    }

    public function isTourComplete(): bool
    {
        return $this->tourComplete;
    }

    public function setTourComplete(): void
    {
        $this->tourComplete = true;
    }

    public function updateWorkshopState(string $workshop, UserState $state): void
    {
        $this->workshopState[$workshop] = [
            'completedExercises' => $state->getCompletedExercises(),
            'currentExercise' => $state->getCurrentExercise(),
        ];
    }

    public function toDTO(): StudentDTO
    {
        return new StudentDTO(
            $this->getId(),
            $this->username,
            $this->email,
            $this->name,
            $this->profilePicture,
            $this->location,
            $this->joinDate,
            $this->tourComplete,
            new StudentCloudState($this->workshopState)
        );
    }

    /**
     * @return array{
     *     username: string,
     *     email: string,
     *     name: string,
     *     profile_picture: ?string,
     *     location: ?string,
     *     join_date: string,
     *     tour_complete: bool,
     *     state: array{
     *          workshops: WorkshopState,
     *          total_completed: int
     *     }
     * }
     */
    public function jsonSerialize(): array
    {
        return $this->toDTO()->jsonSerialize();
    }
}
