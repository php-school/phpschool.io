<?php

namespace PhpSchool\Website\Action;

use Doctrine\ORM\EntityManagerInterface;
use PhpSchool\Website\User\Session;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentLogout
{
    use JsonUtils;

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
        return $this->jsonSuccess($response);
    }
}
