<?php

namespace PhpSchool\Website\User;

use DateTime;
use PhpSchool\Website\Cloud\StudentCloudState;
use Ramsey\Uuid\UuidInterface;

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
        public readonly StudentCloudState $workshopState
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'profile_picture' => $this->profilePicture,
            'location' => $this->location,
            'join_date' => $this->joinDate->format('F Y'),
            'state' => $this->workshopState
        ];
    }
}
