<?php

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\AuthenticationService;
use PhpSchool\Website\Validator\Login as LoginValidator;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class Login
{
    /**
     * @var AuthenticationService
     */
    private $authenticationService;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(AuthenticationService $authenticationService, PhpRenderer $renderer)
    {
        $this->authenticationService = $authenticationService;
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response)
    {
        if ($this->authenticationService->hasIdentity()) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin');
        }

        if ($request->getMethod() === 'GET') {
            return $this->renderer->render($response, 'admin/login.phtml', [
                'pageTitle'       => 'Login to Admin',
                'pageDescription' => 'Login to Admin',
            ]);
        }

        if ($request->getMethod() !== 'POST') {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/');
        }

        $validator = new LoginValidator;

        if (!$validator->validateRequest($request)) {
            return $this->renderer->render($response, 'admin/login.phtml', [
                'pageTitle'       => 'Login to Admin',
                'pageDescription' => 'Login to Admin',
                'messages'        => $validator->getMessages()
            ]);
        }

        $result = $this->authenticationService->login(
            $request->getParam('email'),
            $request->getParam('password')
        );

        if ($result->isValid()) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin');
        }

        return $this->renderer->render($response, 'admin/login.phtml', [
            'pageTitle'       => 'Login to Admin',
            'pageDescription' => 'Login to Admin',
            'messages'        => [$result->getMessages()]
        ]);
    }
}
