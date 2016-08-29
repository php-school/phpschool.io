<?php

namespace PhpSchool\Website\User;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\Authentication\Result;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class AuthenticationService
{
    /**
     * @var AuthenticationServiceInterface
     */
    private $authenticationService;

    public function __construct(AuthenticationServiceInterface $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function login($username, $password) : Result
    {
        $this->authenticationService
            ->getAdapter()
            ->setIdentity($username)
            ->setCredential($password);

        return $this->authenticationService->authenticate();
    }

    public function logout()
    {
        $this->authenticationService->clearIdentity();
    }

    public function hasIdentity() : bool
    {
        return $this->authenticationService->hasIdentity();
    }

    public function getIdentity()
    {
        return $this->authenticationService->getIdentity();
    }
}
