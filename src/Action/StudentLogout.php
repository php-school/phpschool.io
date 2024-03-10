<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action;

use PhpSchool\Website\User\Session;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StudentLogout
{
    use JsonUtils;

    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $this->session->delete('student');
        return $this->jsonSuccess($response);
    }
}
