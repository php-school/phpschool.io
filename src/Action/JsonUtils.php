<?php

namespace PhpSchool\Website\Action;

use Psr\Http\Message\ResponseInterface as Response;

trait JsonUtils
{
    private function jsonSuccess(Response $response): Response
    {
        $response
            ->getBody()
            ->write(json_encode(['success' => true]));

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }

    private function withJson(array $json, Response $response, int $status = 200): Response
    {
        $response
            ->getBody()
            ->write(json_encode($json));

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json');
    }
}
