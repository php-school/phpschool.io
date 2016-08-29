<?php

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\PhpRenderer;
use PhpSchool\Website\User\AuthenticationService;
use Psr\Http\Message\ResponseInterface;
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
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var PhpRenderer
     */
    private $renderer;

    public function __construct(
        AuthenticationService $authenticationService,
        FormHandler $formHandler,
        PhpRenderer $renderer
    ) {
        $this->authenticationService = $authenticationService;
        $this->formHandler           = $formHandler;
        $this->renderer              = $renderer;
    }

    public function showLoginForm(Request $request, Response $response) : Response
    {
        if ($this->authenticationService->hasIdentity()) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', '/admin');
        }

        $this->renderer->appendLocalCss('login', __DIR__ . '/../../../public/css/page-login.css');

        return $this->renderer->render($response, 'admin/login.phtml', [
            'pageTitle'       => 'Login to Admin',
            'pageDescription' => 'Login to Admin',
            'errors'          => $this->formHandler->getPreviousErrors()
        ]);
    }

    public function login(Request $request, Response $response) : Response
    {
        $res = $this->formHandler->validateAndRedirectIfErrors($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res;
        }

        $result = $this->authenticationService->login(
            $request->getParam('email'),
            $request->getParam('password')
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
