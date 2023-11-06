<?php

namespace PhpSchool\Website\Action;

use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\Github;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use PhpSchool\Website\User\Entity\Student;
use PhpSchool\Website\User\Session;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentLogout
{
    use RedirectUtils;

    private Session $session;
    private EntityManagerInterface $entityManager;

    public function __construct(Session $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $this->session->delete('student');
        return $this->redirect('/cloud/workshops');
    }
}
