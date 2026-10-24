<?php

declare(strict_types=1);

namespace PhpSchool\Website\User\Middleware;

use GuzzleHttp\Psr7\Response;
use PhpSchool\Website\Action\RedirectUtils;
use PhpSchool\Website\User\SessionStorageInterface;
use PhpSchool\Website\User\StudentDTO;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class StudentAuthenticator
{
    use RedirectUtils;

    public function __construct(
        private readonly SessionStorageInterface $session,
    ) {}

    public function __invoke(Request $request, RequestHandler $handler): MessageInterface
    {
        //only the online route group uses this middleware, so never skip by path. Slim routes the decoded
        //path, so a raw path check let "/api/%6Fnline/reset" through
        $student = $this->session->get('student');

        if (!$student instanceof StudentDTO) {
            return new Response(401);
        }

        return $handler->handle($request);
    }
}
