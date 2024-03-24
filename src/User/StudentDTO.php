<?php

declare(strict_types=1);

namespace PhpSchool\Website\User;

use DateTime;
use PhpSchool\Website\Online\StudentCloudState;
use Ramsey\Uuid\UuidInterface;

/**
 * @phpstan-import-type WorkshopState from \PhpSchool\Website\Online\StudentCloudState
 */
class StudentDTO implements \JsonSerializable
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $username,
        public readonly string $email,
        public readonly string $name,
        public readonly ?string $profilePicture,
        public readonly ?string $location,
        public readonly DateTime $joinDate,
        public readonly bool $tourComplete,
        public readonly StudentCloudState $workshopState
    ) {}

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
     *         workshops: WorkshopState,
     *         total_completed: int
     *     }
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'profile_picture' => $this->profilePicture,
            'location' => $this->location,
            'join_date' => $this->joinDate->format('F Y'),
            'tour_complete' => $this->tourComplete,
            'state' => $this->workshopState->jsonSerialize()
        ];
    }
}
