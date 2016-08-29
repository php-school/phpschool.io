<?php

namespace PhpSchool\Website\Action;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
trait Redirect
{



    public function redirect(RequestInterface $request, ResponseInterface $response)
    {
        return $response->withRedirect($request->getHeader('referer'));
    }
}
