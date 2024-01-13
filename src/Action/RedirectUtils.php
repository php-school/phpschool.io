<?php

namespace PhpSchool\Website\Action;

use Psr\Http\Message\MessageInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

trait RedirectUtils
{
    private function redirect(string $url): MessageInterface
    {
        return (new GuzzleResponse(302))
            ->withHeader('Location', $url);
    }

    private function redirectToDashboard(): MessageInterface
    {
        return $this->redirect('/online/dashboard');
    }
}
