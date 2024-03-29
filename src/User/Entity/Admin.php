<?php

declare(strict_types=1);

namespace PhpSchool\Website\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="admin_user"))
 */
class Admin implements \JsonSerializable
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
    private string $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $passwordHash;

    public function __construct(string $username, string $email, string $passwordHash)
    {
        $this->username     = $username;
        $this->email        = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return array{
     *     name: string,
     *     email: string,
     *     avatar: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getUsername(),
            'email' => $this->getEmail(),
            'avatar' => 'https://www.gravatar.com/avatar/' . md5($this->getEmail()),
        ];
    }
}
