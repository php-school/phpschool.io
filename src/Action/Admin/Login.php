<?php

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\ViteManifest;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Login
{
    private AdminAuthenticationService $authenticationService;
    private FormHandler $formHandler;
    private PhpRenderer $renderer;
    private ViteManifest $viteManifest;

    public function __construct(
        AdminAuthenticationService $authenticationService,
        FormHandler $formHandler,
        PhpRenderer $renderer,
        ViteManifest $viteManifest
    ) {
        $this->authenticationService = $authenticationService;
        $this->formHandler = $formHandler;
        $this->renderer = $renderer;
        $this->viteManifest = $viteManifest;
    }

    public function showLoginForm(Request $request, Response $response): MessageInterface
    {
        if ($this->authenticationService->hasIdentity()) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin');
        }

        $this->renderer->appendRemoteCss('login', $this->viteManifest->cssUrls('../public/js/login.js')[0]);

        $response = $response->withAddedHeader('Cache-Control', 'no-cache');

        return $this->renderer->render($response, 'admin/login.phtml', [
            'pageTitle'       => 'Login to Admin',
            'pageDescription' => 'Login to Admin',
            'errors'          => $this->formHandler->getPreviousErrors()
        ]);
    }

    public function login(Request $request, Response $response): MessageInterface
    {
        $res = $this->formHandler->validateAndRedirectIfErrors($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        $result = $this->authenticationService->login(
            $this->formHandler->getData()['email'],
            $this->formHandler->getData()['password']
        );

        if (!$result->isValid()) {
            return $this->formHandler->redirectWithErrors($request, $response, [
                $result->getMessages()
            ]);
        }

        return $response
            ->withStatus(302)
            ->withHeader('Location', '/admin');
    }
}
