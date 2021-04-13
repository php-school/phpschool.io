<?php

namespace PhpSchool\Website\User;

use Laminas\Authentication\Adapter\AbstractAdapter;
use Laminas\Authentication\AuthenticationService as LaminasAuthService;
use Laminas\Authentication\Result;
use PhpSchool\Website\User\Entity\User;

class AuthenticationService
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

    /**
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function getIdentity(): ?User
    {
        return $this->authenticationService->getIdentity();
    }
}
