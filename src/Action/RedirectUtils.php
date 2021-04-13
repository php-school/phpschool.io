<?php

namespace PhpSchool\Website\Action;

use GuzzleHttp\Psr7\Response;

trait RedirectUtils
{
    private function redirect(string $url): Response
    {
        return (new Response(302))
            ->withHeader('Location', $url);
    }
}
