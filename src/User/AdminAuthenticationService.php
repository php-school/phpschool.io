<?php

declare(strict_types=1);

namespace PhpSchool\Website\User;

use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\AuthenticationService as LaminasAuthService;
use Laminas\Authentication\Result;
use PhpSchool\Website\User\Entity\Admin;

class AdminAuthenticationService
{
    private LaminasAuthService $authenticationService;

    public function __construct(LaminasAuthService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function login(string $username, string $password): Result
    {
        /** @var AbstractAdapter $adapter */
        $adapter = $this->authenticationService
            ->getAdapter();

        $adapter->setIdentity($username)
            ->setCredential($password);

        return $this->authenticationService->authenticate();
    }

    public function logout(): void
    {
        $this->authenticationService->clearIdentity();
    }

    public function hasIdentity(): bool
    {
        return $this->authenticationService->hasIdentity();
    }

    public function getIdentity(): ?Admin
    {
        /** @var ?Admin $admin */
        $admin = $this->authenticationService->getIdentity();
        return $admin;
    }
}
