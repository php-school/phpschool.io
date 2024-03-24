<?php

declare(strict_types=1);

namespace PhpSchool\Website\Action;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Contributors
{
    use JsonUtils;
    public function __invoke(Request $request, Response $response): Response
    {
        /** @var array<string, array{username: string, contributions: int, profilePic: string, profile: string}> $data */
        $data = json_decode((string) file_get_contents(__DIR__ . '/../../var/contributors.json'));
        return $this->withJson($data, $response);
    }
}
