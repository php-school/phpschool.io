<?php

namespace PhpSchool\Website\User;

use Ramsey\Uuid\UuidInterface;

interface Student
{
    public function getEmail(): string;

    public function getUsername(): string;

    public function getName(): string;

    public function getProfilePicture(): ?string;

    public function getLocation(): ?string;

    public function jsonSerialize(): array;
}
