<?php

namespace PhpSchool\Website\Cloud\Action;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Cloud\StudentWorkshopState;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ResetState
{
    use JsonUtils;

    public function __construct(
        private readonly StudentWorkshopState $studentState,
    ) {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        //$this->studentState->reset();

        return $this->jsonSuccess($response);
    }
}