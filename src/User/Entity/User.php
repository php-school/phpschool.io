<?php

namespace PhpSchool\Website\User\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;


/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 *
 * @ORM\Entity
 * @ORM\Table(name="user"))
 */
class User
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $passwordHash;

    public function __construct(string $username, string $email, string $passwordHash)
    {
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->passwordHash;
    }
}
