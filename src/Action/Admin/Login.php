<?php

namespace PhpSchool\Website\Action\Admin;

use PhpSchool\Website\Action\JsonUtils;
use PhpSchool\Website\Form\FormHandler;
use PhpSchool\Website\User\AdminAuthenticationService;
use PhpSchool\Website\User\Entity\Admin;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;

/**
 * @phpstan-import-type LoginData from \PhpSchool\Website\InputFilter\Login
 */
class Login
{
    use JsonUtils;

    /**
     * @param FormHandler<LoginData> $formHandler
     */
    public function __construct(
        private readonly AdminAuthenticationService $authenticationService,
        private readonly FormHandler $formHandler,
        private readonly string $jwtSecret,
    ) {
    }

    public function __invoke(Request $request, Response $response): MessageInterface
    {
        $res = $this->formHandler->validateJsonRequest($request, $response);

        if ($res instanceof ResponseInterface) {
            return $res->withStatus(401);
        }

        $result = $this->authenticationService->login(
            $this->formHandler->getData()['email'],
            $this->formHandler->getData()['password']
        );

        if (!$result->isValid()) {
            return $this->withJson([
                'success' => false,
                'form_errors' => ['auth' => $result->getMessages()],
            ], $response, 401);
        }

        /** @var Admin $admin */
        $admin = $result->getIdentity();

        $token = [
            "iss" => "https://www.phpschool.io",
            "iat" => time(),
            "exp" => time() + 3600, //1hr
            "data" => [
                "adminId" => $admin->getId()->toString(),
            ]
        ];

        return $this->withJson([
            'success' => true,
            'token' => JWT::encode($token, $this->jwtSecret),
            'admin' => $admin,
        ], $response);
    }
}
