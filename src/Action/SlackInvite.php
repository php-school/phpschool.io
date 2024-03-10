<?php

namespace PhpSchool\Website\Action;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SlackInvite
{
    use JsonUtils;

    public function __construct(private readonly Client $client, private readonly string $slackApiToken)
    {
    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        if (!is_array($data) && !isset($data['email'])) {
            return $this->withJson(['error' => 'Email not set'], $response, 500);
        }

        try {
            $apiResponse = $this->client->post('https://phpschool-team.slack.com/api/users.admin.invite', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'token' => $this->slackApiToken,
                    'email' => $data['email'],
                    'set_active' => true,
                ],
            ]);
        } catch (\Exception $e) {
            return $this->withJson(['error' => $e->getMessage()], $response, 500);
        }

        $apiResponseData = json_decode($apiResponse->getBody()->__toString(), true);

        if (!is_array($apiResponseData)) {
            return $this->withJson(['error' => 'An unknown error occurred'], $response, 500);
        }

        if (isset($apiResponseData['ok']) && $apiResponseData['ok'] === true) {
            return $this->jsonSuccess($response);
        }

        if (isset($apiResponseData['error'])) {
            $error = match ($apiResponseData['error']) {
                'already_in_team_invited_user' => 'You have already been invited to the PHP School Slack',
                'already_in_team' => 'You are already a member of the PHP School Slack',
                'invalid_email' => 'The email address you entered is not valid',
                'not_authed' => 'An error occurred while trying to authenticate your request',
                default => $apiResponseData['error']
            };

            return $this->withJson(['error' => $error], $response, 500);
        }

        return $this->withJson(['error' => 'An unknown error occurred'], $response, 500);
    }
}
