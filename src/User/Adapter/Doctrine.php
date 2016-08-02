<?php

namespace PhpSchool\Website\User\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\User\Entity\User;
use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Adapter\Exception\ExceptionInterface;
use Zend\Authentication\Result as AuthenticationResult;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Doctrine extends AbstractAdapter implements AdapterInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Performs an authentication attempt
     *
     * @return AuthenticationResult
     * @throws ExceptionInterface If authentication cannot be performed
     */
    public function authenticate() : AuthenticationResult
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $this->getIdentity()]);

        if ($user === null) {
            return new AuthenticationResult(
                AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND,
                null,
                ['User not found.']
            );
        }

        if (password_verify($this->credential, $user->getPassword())) {
            return new AuthenticationResult(AuthenticationResult::SUCCESS, $user);
        }

        return new AuthenticationResult(
            AuthenticationResult::FAILURE_CREDENTIAL_INVALID,
            null,
            ['Invalid username or password provided']
        );
    }
}
