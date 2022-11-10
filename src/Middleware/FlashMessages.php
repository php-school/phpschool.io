<?php

namespace PhpSchool\Website\Middleware;

use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\FlashMessages as Messages;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class FlashMessages
{
    private Messages $messages;
    private PhpRenderer $phpRenderer;

    public function __construct(Messages $messages, PhpRenderer $phpRenderer)
    {
        $this->messages = $messages;
        $this->phpRenderer = $phpRenderer;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $this->phpRenderer
            ->addAttribute('successMessages', $this->messages->getMessage('success') ?? []);

        $this->phpRenderer
            ->addAttribute('errorMessages', $this->messages->getMessage('error') ?? []);

        return $handler->handle($request);
    }
}
