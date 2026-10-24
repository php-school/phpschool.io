<?php

declare(strict_types=1);

namespace PhpSchool\Website\User\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\User\Entity\Admin;
use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result as AuthenticationResult;

class Doctrine extends AbstractAdapter implements AdapterInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function authenticate(): AuthenticationResult
    {
        $admin = $this->entityManager->getRepository(Admin::class)->findOneBy(['email' => $this->getIdentity()]);

        if ($admin === null) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND,
                null,
                //same message as a wrong password, so the form doesn't reveal which emails are admins
                ['Invalid username or password provided'],
            );
        }

        if (!is_string($this->credential)) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
                null,
                ['No credential found.'],
            );
        }

        if (password_verify($this->credential, $admin->getPassword())) {
            return new AuthenticationResult(AuthenticationResult::SUCCESS, $admin);
        }

        return new AuthenticationResult(
            AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid username or password provided'],
        );
    }
}
